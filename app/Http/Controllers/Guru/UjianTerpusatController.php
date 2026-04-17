<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Schedule;

class UjianTerpusatController extends Controller
{
    /**
     * Menampilkan daftar jadwal ujian yang diplot oleh Admin untuk guru ini.
     * Menu: Ujian Terpusat
     */
    public function index()
    {
        $teacherId = auth()->id();

        // Mengambil jadwal dari Admin yang diampu oleh guru ini
        $schedules = Schedule::with(['subject', 'classroom'])
            ->where(function($query) use ($teacherId) {
                $query->whereJsonContains('teacher_ids', (string)$teacherId)
                      ->orWhere('teacher_ids', $teacherId);
            })
            ->latest()
            ->get();

        // Path view diganti ke folder ujian_terpusat
        return view('guru.ujian_terpusat.index', compact('schedules'));
    }

    /**
     * Mengelola daftar soal untuk jadwal ujian tertentu.
     */
    public function manage($schedule_id)
    {
        // Ambil data jadwal terpusat dari Admin
        $schedule = Schedule::with(['subject', 'classroom'])->findOrFail($schedule_id);

        // Ambil soal yang sudah dibuat guru untuk mapel ini
        // Menggunakan subject_id agar soal bersifat reusable untuk kelas lain dengan mapel sama
        $questions = Question::where('subject_id', $schedule->subject_id)
                             ->where('user_id', auth()->id())
                             ->latest()
                             ->get();

        // Path view diganti ke folder ujian_terpusat
        return view('guru.ujian_terpusat.manage', compact('schedule', 'questions'));
    }

    /**
     * Form tambah soal yang otomatis membawa subject_id dari jadwal
     */
    public function create(Request $request)
    {
        $subject_id = $request->subject_id;
        $schedule_id = $request->schedule_id; // Untuk keperluan redirect kembali nanti

        return view('guru.ujian_terpusat.create', compact('subject_id', 'schedule_id'));
    }
}