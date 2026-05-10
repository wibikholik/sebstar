<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StudentAnswer;
use App\Models\User;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;
use App\Exports\NilaiUjianExport;
use Maatwebsite\Excel\Facades\Excel;

class KoreksiController extends Controller
{
    /**
     * 1. Menampilkan daftar jadwal ujian (Dipanggil dari Sidebar)
     */
    public function listSchedules()
    {
        // Menggunakan whereJsonContains karena teacher_ids bertipe JSON
        $schedules = Schedule::whereJsonContains('teacher_ids', (string) auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('guru.koreksi.list_jadwal', compact('schedules'));
    }

    /**
     * 2. Menampilkan daftar siswa & pengaturan bobot per jadwal
     * URL: /guru/koreksi/jadwal/{schedule_id}
     */
    public function index(Request $request, $schedule_id)
    {
        $schedule = Schedule::findOrFail($schedule_id);

        // Ambil siswa yang punya jawaban di jadwal ini beserta hitungan status koreksinya
        $students = User::whereHas('answers', function($q) use ($schedule_id) {
            $q->where('schedule_id', $schedule_id);
        })
        ->withCount(['answers as total_essay' => function($q) use ($schedule_id) {
            $q->where('schedule_id', $schedule_id)
              ->whereHas('question', fn($query) => $query->where('type', 'essay'));
        }])
        ->withCount(['answers as graded_essay' => function($q) use ($schedule_id) {
            $q->where('schedule_id', $schedule_id)
              ->where('is_graded', true)
              ->whereHas('question', fn($query) => $query->where('type', 'essay'));
        }])
        ->get();

        return view('guru.koreksi.index', compact('students', 'schedule'));
    }

    /**
     * 3. Menyimpan pengaturan bobot nilai (Dipanggil dari Index Monitoring)
     */
    public function storeWeight(Request $request, $schedule_id)
    {
        $request->validate([
            'weight_pg' => 'required|numeric|min:0|max:100',
            'weight_essay' => 'required|numeric|min:0|max:100',
        ]);

        $schedule = Schedule::findOrFail($schedule_id);
        $schedule->update([
            'weight_pg' => $request->weight_pg,
            'weight_essay' => $request->weight_essay,
        ]);

        return redirect()->back()->with('success', 'Bobot nilai berhasil diperbarui untuk mobile siswa!');
    }

    /**
     * 4. Form periksa jawaban individu (Hanya Essay)
     */
    public function show(Request $request, $user_id)
    {
        $schedule_id = $request->query('schedule_id');
        if (!$schedule_id) {
            return redirect()->route('guru.koreksi.list')->with('error', 'Pilih jadwal terlebih dahulu.');
        }

        $student = User::findOrFail($user_id);
        $schedule = Schedule::findOrFail($schedule_id);

        $essayAnswers = StudentAnswer::with('question')
            ->where('schedule_id', $schedule_id)
            ->where('user_id', $user_id)
            ->whereHas('question', function($query) {
                $query->where('type', 'essay');
            })->get();

        return view('guru.koreksi.periksa', compact('student', 'schedule', 'essayAnswers'));
    }

    /**
     * 5. Simpan hasil penilaian essay
     */
    public function update(Request $request, $student_id)
    {
        // Validasi skor agar tidak tumpang tindih
        if ($request->has('scores')) {
            foreach ($request->scores as $answerId => $score) {
                StudentAnswer::where('id', $answerId)->update([
                    'score' => $score,
                    'teacher_note' => $request->notes[$answerId] ?? null,
                    'is_graded' => true
                ]);
            }
        }

        return redirect()->route('guru.koreksi.index', $request->schedule_id)
                         ->with('success', 'Penilaian berhasil disimpan!');
    }

    /**
     * 6. Export Nilai ke Excel (Mengambil bobot yang sudah tersimpan di DB)
     */
    public function exportExcel(Request $request, $schedule_id)
    {
        $schedule = Schedule::findOrFail($schedule_id);
        
        // Menggunakan bobot dari database, fallback ke default 60/40 jika belum di-set
        $bobotPg = $schedule->weight_pg ?? 60;
        $bobotEssay = $schedule->weight_essay ?? 40;

        $namaFile = 'Nilai_' . str_replace(' ', '_', $schedule->subject->nama_mapel) . '_' . date('Ymd') . '.xlsx';

        return Excel::download(
            new NilaiUjianExport($schedule_id, $bobotPg, $bobotEssay), 
            $namaFile
        );
    }
}