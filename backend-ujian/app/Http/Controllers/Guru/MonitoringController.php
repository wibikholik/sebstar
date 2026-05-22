<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\User;
use App\Models\ExamLog; 
use App\Models\StudentAnswer;
use App\Events\ExamMonitoringEvent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MonitoringController extends Controller
{
    /**
     * Menampilkan daftar jadwal ujian yang SEDANG AKTIF DIAWASI oleh guru ini.
     */
    public function index()
    {
        $userId = Auth::id();

        // PERBAIKAN: Menambahkan filter di mana status jadwal murni 'aktif' atau 'nonaktif' (siaga)
        // Jadwal yang statusnya sudah 'selesai' atau 'dihapus' otomatis ditendang (tidak ditampilkan)
        $schedules = Schedule::with(['subject', 'classroom'])
            ->where(function($query) use ($userId) {
                $query->where('proctor_id', $userId)
                      ->orWhereJsonContains('teacher_ids', (string)$userId)
                      ->orWhereJsonContains('teacher_ids', (int)$userId);
            })
            ->whereIn('status', ['aktif', 'nonaktif']) // 🌟 KUNCI PERBAIKAN DI SINI!
            ->latest()
            ->get();

        return view('guru.monitoring.index', compact('schedules'));
    }

    /**
     * Menampilkan halaman detail monitoring (Real-time Live Monitoring Siswa).
     */
    public function show($id)
    {
        $userId = Auth::id();

        // 1. Ambil data jadwal beserta relasinya
        $schedule = Schedule::with(['subject', 'classroom'])->findOrFail($id);

        // 2. Keamanan Tingkat Tinggi: Pastikan pengakses beneran bagian dari tim pengawas/pengampu jadwal ini
        $isProctor = $schedule->proctor_id === $userId;
        $isTeacherInvolved = is_array($schedule->teacher_ids) && (in_array((string)$userId, $schedule->teacher_ids) || in_array((int)$userId, $schedule->teacher_ids));

        if (!$isProctor && !$isTeacherInvolved) {
            abort(403, 'Akses ditolak! Anda tidak memiliki otoritas pengawasan untuk ruang ujian ini.');
        }

        /**
         * 3. Ambil daftar siswa beserta rekapitulasi real-time progress jawaban & pelanggaran
         */
        $students = User::where('role', 'siswa')
            ->where('classroom_id', $schedule->classroom_id)
            ->withCount([
                // Hitung jumlah butir soal yang sudah dijawab oleh siswa pada sesi jadwal ini
                'studentAnswers as total_dijawab' => function ($query) use ($schedule) {
                    $query->where('schedule_id', $schedule->id);
                },
                // Hitung total log pelanggaran murni keluar dari aplikasi kuis mobile SEBSTAR
                'examLogs as total_pelanggaran' => function ($query) use ($schedule) {
                    $query->where('schedule_id', $schedule->id)
                          ->whereIn('type', ['keluar_aplikasi', 'KELUAR_APLIKASI']); 
                }
            ])
            ->orderBy('name', 'asc')
            ->get();

        // Memeriksa status penguncian FORCE_SUBMIT siswa secara dinamis lewat koleksi logs
        foreach ($students as $student) {
            $student->is_force_submitted = ExamLog::where('schedule_id', $schedule->id)
                ->where('user_id', $student->id)
                ->where('type', 'FORCE_SUBMIT')
                ->exists();
        }

        // Ambil 10 baris riwayat log aktivitas pelanggaran terbaru untuk pakan umpan balik (live-feed) bawah
        $recentLogs = ExamLog::with('user')
            ->where('schedule_id', $schedule->id)
            ->latest()
            ->take(10)
            ->get();

        return view('guru.monitoring.show', compact('schedule', 'students', 'recentLogs'));
    }

    /**
     * Fitur: Reset Login, Pelanggaran, dan Lembar Jawaban Siswa (Mulai Ulang Sesi 0%)
     */
    public function resetStudent(Request $request, $schedule_id, $student_id)
    {
        $userId = Auth::id();
        $schedule = Schedule::findOrFail($schedule_id);
        $student = User::where('id', $student_id)->where('role', 'siswa')->firstOrFail();

        // Proteksi Otoritas Sesi Pengawas
        $isProctor = $schedule->proctor_id === $userId;
        $isTeacherInvolved = is_array($schedule->teacher_ids) && (in_array((string)$userId, $schedule->teacher_ids) || in_array((int)$userId, $schedule->teacher_ids));

        if (!$isProctor && !$isTeacherInvolved) {
            return redirect()->back()->with('error', 'Aksi ditolak! Anda tidak memiliki akses moderasi pada ruang ujian ini.');
        }

        try {
            DB::transaction(function () use ($schedule_id, $student_id, $student) {
                // 1. Lepas kunci login siswa agar bisa login ulang dari perangkat mobile
                $student->update(['is_logged_in' => 0]);

                // 2. Bersihkan seluruh rekaman kecurangan keluar aplikasi & log force submit
                ExamLog::where('schedule_id', $schedule_id)
                    ->where('user_id', $student_id)
                    ->whereIn('type', ['keluar_aplikasi', 'KELUAR_APLIKASI', 'FORCE_SUBMIT'])
                    ->delete();

                // 3. Bersihkan butir lembar jawaban lama agar bar persentase pengerjaan guru mundur ke 0%
                StudentAnswer::where('schedule_id', $schedule_id)
                    ->where('user_id', $student_id)
                    ->delete();
            });

            // 📢 4. BROADCAST LIVE REVERB: Sinyal pemulihan akses kuis baru ke HP perangkat siswa terpilih
            broadcast(new ExamMonitoringEvent($schedule_id, $student_id, 'RESET_AKSES', "Akses sesi pengerjaan ujian siswa {$student->name} telah dipulihkan penuh dari pusat."))->toOthers();

            return redirect()->back()->with('success', "Akses pengerjaan siswa \"{$student->name}\" berhasil dibersihkan total. Progress kembali ke awal 0%.");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses reset siswa: ' . $e->getMessage());
        }
    }

    /**
     * Fitur: Perbarui Status Operasional Server Ujian (Aktifkan / Force Stop)
     */
    public function updateStatus(Request $request, $id)
    {
        $userId = Auth::id();
        
        $request->validate([
            'status' => 'required|in:aktif,nonaktif,selesai'
        ]);

        try {
            $schedule = Schedule::findOrFail($id);

            $isProctor = $schedule->proctor_id === $userId;
            $isTeacherInvolved = is_array($schedule->teacher_ids) && (in_array((string)$userId, $schedule->teacher_ids) || in_array((int)$userId, $schedule->teacher_ids));

            if (!$isProctor && !$isTeacherInvolved) {
                return redirect()->back()->with('error', 'Aksi diblokir! Anda tidak berwenang mengendalikan status server ujian ruangan ini.');
            }

            $schedule->update(['status' => $request->status]);

            $statusText = match ($request->status) {
                'aktif'    => 'DIAKTIFKAN KEMBALI',
                'selesai'  => 'DIHENTIKAN PAKSA (FORCE STOP)',
                default    => 'DINONAKTIFKAN',
            };

            return redirect()->back()->with('success', "Status pengerjaan server ujian berhasil diubah menjadi {$statusText}!");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal merubah status operasional server: ' . $e->getMessage());
        }
    }

    /**
     * Fitur: Hentikan & Selesaikan Sesi Ujian Siswa Secara Paksa (Force Submit)
     */
    public function forceSubmit($schedule_id, $student_id)
    {
        $userId = Auth::id();
        $schedule = Schedule::findOrFail($schedule_id);
        $student = User::where('id', $student_id)->where('role', 'siswa')->firstOrFail();

        $isProctor = $schedule->proctor_id === $userId;
        $isTeacherInvolved = is_array($schedule->teacher_ids) && (in_array((string)$userId, $schedule->teacher_ids) || in_array((int)$userId, $schedule->teacher_ids));

        if (!$isProctor && !$isTeacherInvolved) {
            return redirect()->back()->with('error', 'Aksi ditolak! Anda bukan bagian dari tim pengawas ruangan kuis ini.');
        }

        try {
            DB::transaction(function () use ($student, $schedule) {
                // 1. Putuskan status login perangkat anak
                $student->update(['is_logged_in' => 0]);

                // 2. Tanam log FORCE_SUBMIT
                ExamLog::updateOrCreate(
                    [
                        'schedule_id' => $schedule->id,
                        'user_id'     => $student->id,
                        'type'        => 'FORCE_SUBMIT'
                    ],
                    [
                        'details'     => 'Sesi pengerjaan lembar ujian siswa telah dihentikan paksa dan dikunci permanen oleh Guru Pengawas Ruangan.'
                    ]
                );
            });

            // 📢 3. BROADCAST LIVE VIA REVERB
            broadcast(new ExamMonitoringEvent($schedule->id, $student->id, 'FORCE_SUBMIT', "Sesi pengerjaan kuis Anda telah diselesaikan secara paksa oleh Pengawas Ruangan."))->toOthers();

            return redirect()->back()->with('success', "Sesi ujian siswa \"{$student->name}\" berhasil dihentikan dan dikunci secara sepihak!");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal memproses tindakan force submit: ' . $e->getMessage());
        }
    }
}