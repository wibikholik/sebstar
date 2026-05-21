<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\User;
use App\Models\ExamLog; 
use App\Models\StudentAnswer; // 🛑 Di-import untuk membersihkan lembar jawaban saat mengulang
use App\Events\ExamMonitoringEvent; // 📢 Memastikan jabat tangan live Reverb WebSocket aktif
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MonitoringController extends Controller
{
    /**
     * Menampilkan daftar jadwal ujian yang diawasi oleh guru ini.
     */
    public function index()
    {
        $userId = Auth::id();

        $schedules = Schedule::with(['subject', 'classroom'])
            ->where('proctor_id', $userId)
            ->latest()
            ->get();

        return view('guru.monitoring.index', compact('schedules'));
    }

    /**
     * Menampilkan halaman detail monitoring (Real-time Monitoring Siswa).
     */
    public function show($id)
    {
        // 1. Ambil data jadwal beserta relasinya
        $schedule = Schedule::with(['subject', 'classroom'])->findOrFail($id);

        // 2. Keamanan: Pastikan yang mengakses adalah pengawas yang ditugaskan
        if ($schedule->proctor_id !== Auth::id()) {
            abort(403, 'Anda tidak memiliki hak akses untuk memonitor jadwal ini.');
        }

        /**
         * 3. Ambil daftar siswa beserta summary jawaban & jumlah pelanggaran.
         */
        $students = User::where('role', 'siswa')
            ->where('classroom_id', $schedule->classroom_id)
            ->withCount([
                // Hitung berapa soal yang sudah dijawab oleh siswa pada jadwal ini
                'studentAnswers as total_dijawab' => function ($query) use ($schedule) {
                    $query->where('schedule_id', $schedule->id);
                },
                // 🛠️ PERBAIKAN: Masukkan tipe 'FORCE_SUBMIT' agar boks summary statistik atas ikut sinkron membaca status terkunci
                'examLogs as total_pelanggaran' => function ($query) use ($schedule) {
                    $query->where('schedule_id', $schedule->id)
                          ->whereIn('type', ['keluar_aplikasi', 'KELUAR_APLIKASI', 'FORCE_SUBMIT']); 
                }
            ])
            ->orderBy('name', 'asc')
            ->get();

        // Ambil riwayat log pelanggaran terbaru untuk feed bawah
        $recentLogs = ExamLog::with('user')
            ->where('schedule_id', $id)
            ->latest()
            ->take(10)
            ->get();

        return view('guru.monitoring.show', compact('schedule', 'students', 'recentLogs'));
    }

    /**
     * Fitur: Reset Login, Pelanggaran, dan JAWABAN Siswa (Mulai Ulang Penuh dari 0%)
     */
    public function resetStudent(Request $request, $schedule_id, $student_id)
    {
        $student = User::where('id', $student_id)->where('role', 'siswa')->firstOrFail();

        // Menggunakan Database Transaction agar proses pembersihan berjalan serentak dan aman
        DB::transaction(function () use ($schedule_id, $student_id, $student) {
            // 1. Ubah is_logged_in kembali ke 0 agar siswa bisa masuk lagi dari HP-nya
            $student->update([
                'is_logged_in' => 0
            ]);

            // 2. Hapus log pelanggaran keluar_aplikasi & force submit agar status DISKUALIFIKASI di web guru hilang total
            ExamLog::where('schedule_id', $schedule_id)
                ->where('user_id', $student_id)
                ->whereIn('type', ['keluar_aplikasi', 'KELUAR_APLIKASI', 'FORCE_SUBMIT'])
                ->delete();

            // 3. 🛑 PERBAIKAN LOGIKA UTAMA: Hapus lembar jawaban lama agar progress bar di monitor guru mundur bersih ke 0%
            StudentAnswer::where('schedule_id', $schedule_id)
                ->where('user_id', $student_id)
                ->delete();
        });

        // 📢 4. BROADCAST REAL-TIME: Kirim sinyal ke HP siswa agar otomatis mematikan sirine alarm & membuka layar ujian kuis baru
        broadcast(new ExamMonitoringEvent($schedule_id, $student_id, 'RESET_AKSES', "Akses ujian siswa {$student->name} telah di-reset penuh."))->toOthers();

        return redirect()->back()->with('success', "Akses ujian siswa {$student->name} berhasil di-reset penuh. Progress pengerjaan kembali ke 0%.");
    }

    /**
     * Fitur: Update Status Operasional Ujian (Force Stop / Aktifkan)
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:aktif,nonaktif,selesai'
        ]);

        $schedule = Schedule::findOrFail($id);

        if ($schedule->proctor_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki hak akses mengubah status jadwal ini.');
        }

        $schedule->update([
            'status' => $request->status
        ]);

        if ($request->status == 'aktif') {
            $statusText = 'DIAKTIFKAN KEMBALI';
        } elseif ($request->status == 'selesai') {
            $statusText = 'DIHENTIKAN PAKSA (FORCE STOP)';
        } else {
            $statusText = 'DINONAKTIFKAN';
        }

        return redirect()->back()->with('success', "Status server ujian berhasil diubah menjadi {$statusText}.");
    }

    /**
     * Fitur: Selesaikan Paksa Ujian Per-Siswa (Force Submit)
     */
    public function forceSubmit($schedule_id, $student_id)
    {
        $schedule = Schedule::findOrFail($schedule_id);
        $student = User::where('id', $student_id)->where('role', 'siswa')->firstOrFail();

        if ($schedule->proctor_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Aksi ditolak. Anda bukan pengawas ruangan ini.');
        }

        // Jalankan database transaction agar data aman terkunci
        DB::transaction(function () use ($student, $schedule) {
            // 1. Matikan status login siswa
            $student->update([
                'is_logged_in' => 0
            ]);

            // 2. KUNCI AKSES PERMANEN: Tambahkan log bertipe FORCE_SUBMIT ke database.
            // Digunakan agar ketika siswa mencoba iseng masuk lagi lewat HP, API getSoal() otomatis memblokirnya.
            ExamLog::updateOrCreate(
                [
                    'schedule_id' => $schedule->id,
                    'user_id' => $student->id,
                    'type' => 'FORCE_SUBMIT'
                ],
                [
                    'details' => 'Sesi ujian siswa telah dihentikan dan dikunci secara sepihak oleh Guru Pengawas.'
                ]
            );
        });

        // 📢 3. BROADCAST REAL-TIME: Kirim sinyal paksa ke HP siswa via Reverb agar layar pengerjaan menutup otomatis saat itu juga!
        broadcast(new ExamMonitoringEvent($schedule->id, $student->id, 'FORCE_SUBMIT', "Sesi ujian diselesaikan secara paksa oleh Pengawas."))->toOthers();

        return redirect()->back()->with('success', "Ujian siswa {$student->name} berhasil diselesaikan secara paksa oleh Guru.");
    }
}