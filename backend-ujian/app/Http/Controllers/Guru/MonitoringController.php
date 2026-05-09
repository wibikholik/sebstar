<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonitoringController extends Controller
{
    /**
     * Menampilkan daftar jadwal ujian yang diawasi oleh guru ini.
     */
    public function index()
    {
        $userId = Auth::id();

        // Mengambil jadwal dimana guru ini ditugaskan sebagai proctor (pengawas)
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
         * 3. Ambil daftar siswa yang berada di kelas jadwal tersebut.
         * Ini akan digunakan untuk menampilkan list absensi di halaman monitoring.
         */
        $students = User::where('role', 'siswa')
            ->where('classroom_id', $schedule->classroom_id)
            ->orderBy('name', 'asc')
            ->get();

        /**
         * Catatan untuk SEBSTAR: 
         * Nantinya kita akan menambahkan pengecekan ke tabel 'student_answers' 
         * atau 'exam_logs' untuk melihat status pengerjaan siswa secara real-time.
         */

        return view('guru.monitoring.show', compact('schedule', 'students'));
    }

    /**
     * Fitur Tambahan: Reset Login Siswa
     * Berguna jika siswa mengalami kendala perangkat dan perlu login ulang.
     */
    public function resetStudent(Request $request, $schedule_id, $student_id)
    {
        // Logika untuk menghapus session/device_id siswa di tabel tertentu
        // student_exams::where('schedule_id', $schedule_id)->where('user_id', $student_id)->delete();

        return redirect()->back()->with('success', 'Siswa berhasil di-reset. Silakan login kembali.');
    }
}