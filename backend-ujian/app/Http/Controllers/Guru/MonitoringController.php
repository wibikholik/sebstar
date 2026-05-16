<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\User;
use App\Models\ExamLog; // Pastikan Model ExamLog di-import
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
         * Kita gunakan subquery left join agar performa query tetap ringan saat dibaca real-time.
         */
        $students = User::where('role', 'siswa')
            ->where('classroom_id', $schedule->classroom_id)
            ->withCount([
                // Hitung berapa soal yang sudah dijawab oleh siswa pada jadwal ini
                'studentAnswers as total_dijawab' => function ($query) use ($schedule) {
                    $query->where('schedule_id', $schedule->id);
                },
                // Hitung total pelanggaran keluar aplikasi yang dilakukan siswa
                'examLogs as total_pelanggaran' => function ($query) use ($schedule) {
                    $query->where('schedule_id', $schedule->id)
                          ->where('type', 'keluar_aplikasi');
                }
            ])
            ->orderBy('name', 'asc')
            ->get();

        // Ambil riwayat log pelanggaran terbaru untuk ditampilkan di feed notifikasi samping/bawah web
        $recentLogs = ExamLog::with('user')
            ->where('schedule_id', $id)
            ->latest()
            ->take(10)
            ->get();

        return view('guru.monitoring.show', compact('schedule', 'students', 'recentLogs'));
    }

    /**
     * Fitur: Reset Login & Status Kunci Siswa
     */
    public function resetStudent(Request $request, $schedule_id, $student_id)
    {
        // Cari user siswa tersebut
        $student = User::where('id', $student_id)->where('role', 'siswa')->firstOrFail();

        // 1. Ubah is_logged_in kembali ke 0 agar siswa bisa masuk lagi
        $student->update([
            'is_logged_in' => 0
        ]);

        // 2. Opsional: Jika ingin menghapus riwayat pelanggarannya saat di-reset oleh guru, aktifkan ini:
        // ExamLog::where('schedule_id', $schedule_id)->where('user_id', $student_id)->delete();

        return redirect()->back()->with('success', "Akses ujian siswa {$student->name} berhasil di-reset. Silakan minta siswa login kembali.");
    }
}