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
use App\Models\Question;
class KoreksiController extends Controller
{
    /**
     * 1. Menampilkan daftar jadwal ujian (Dipanggil dari Sidebar)
     */
    public function listSchedules()
    {
        $schedules = Schedule::whereJsonContains('teacher_ids', (string) auth()->id())
            ->withCount(['answers as total_essay_count' => function($q) {
                $q->whereHas('question', function($query) {
                    $query->where('type', 'essay');
                });
            }])
            ->withCount(['answers as graded_essay_count' => function($q) {
                $q->where('is_graded', true)
                  ->whereHas('question', function($query) {
                      $query->where('type', 'essay');
                  });
            }])
            ->orderBy('created_at', 'desc')
            ->get();
        
        return view('guru.koreksi.list_jadwal', compact('schedules'));
    }

    /**
     * 2. Menampilkan daftar siswa & kalkulasi nilai murni terintegrasi
     */
    public function index(Request $request, $schedule_id)
    {
        $schedule = Schedule::findOrFail($schedule_id);

        // Ambil siswa yang memiliki jawaban di jadwal ini beserta ringkasan metrik nilainya
        $students = User::whereHas('answers', function($q) use ($schedule_id) {
            $q->where('schedule_id', $schedule_id);
        })
        // Hitung Progress Essay
        ->withCount(['answers as total_essay' => function($q) use ($schedule_id) {
            $q->where('schedule_id', $schedule_id)
              ->whereHas('question', fn($query) => $query->where('type', 'essay'));
        }])
        ->withCount(['answers as graded_essay' => function($q) use ($schedule_id) {
            $q->where('schedule_id', $schedule_id)
              ->where('is_graded', true)
              ->whereHas('question', fn($query) => $query->where('type', 'essay'));
        }])
        // Hitung total soal PG yang dijawab siswa
        ->withCount(['answers as total_pg' => function($q) use ($schedule_id) {
            $q->where('schedule_id', $schedule_id)
              ->whereHas('question', fn($query) => $query->where('type', 'pg'));
        }])
        // Hitung jumlah jawaban PG siswa yang BENAR dengan menyilangkan data ke tabel questions
        ->withCount(['answers as benar_pg' => function($q) use ($schedule_id) {
            $q->where('schedule_id', $schedule_id)
              ->whereHas('question', function($query) {
                  $query->where('type', 'pg')
                        ->whereRaw('student_answers.answer = questions.correct_answer'); 
              });
        }])
        // Ambil rata-rata nilai essay (Skala otomatis dikonversi kembali ke 0 - 100)
        ->withAvg(['answers as avg_skor_essay' => function($q) use ($schedule_id) {
            $q->where('schedule_id', $schedule_id)
              ->where('is_graded', true)
              ->whereHas('question', fn($query) => $query->where('type', 'essay'));
        }], 'score')
        ->get();

        return view('guru.koreksi.index', compact('students', 'schedule'));
    }

    /**
     * 3. Menyimpan pengaturan bobot nilai
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

        return redirect()->back()->with('success', 'Bobot nilai berhasil diperbarui!');
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

        // Ambil seluruh master soal tipe essay yang ada di mata pelajaran/subject jadwal ini
        // Kita gunakan eager loading untuk menarik jawaban milik siswa bersangkutan (jika ada)
        $essayQuestions = Question::where('subject_id', $schedule->subject_id)
            ->where('type', 'essay')
            ->with(['studentAnswers' => function($query) use ($user_id, $schedule_id) {
                $query->where('user_id', $user_id)
                      ->where('schedule_id', $schedule_id);
            }])
            ->get();

        return view('guru.koreksi.periksa', compact('student', 'schedule', 'essayQuestions', 'user_id', 'schedule_id'));
    }

    /**
     * 5. Simpan hasil penilaian essay (Mendukung pembuatan record untuk soal kosong tanpa error database)
     */
    public function update(Request $request, $student_id)
    {
        $schedule_id = $request->schedule_id;

        if ($request->has('scores')) {
            foreach ($request->scores as $inputKey => $score) {
                
                // Pengecekan aman: Jika key mengandung kata 'new_', berarti ini soal kosong baru
                if (is_string($inputKey) && strpos($inputKey, 'new_') !== false) {
                    // Ambil ID master question asli di belakang kata 'new_'
                    $questionId = str_replace('new_', '', $inputKey);
                    
                    // Gunakan updateOrCreate untuk mencegah double rekap data kosong
                    StudentAnswer::updateOrCreate(
                        [
                            'user_id'     => $student_id,
                            'schedule_id' => $schedule_id,
                            'question_id' => $questionId,
                        ],
                        [
                            // 📢 FIX UTAMA: Jangan kirim null jika kolom DB diset NOT NULL. 
                            // Kita ganti dengan string penanda strip atau teks informatif
                            'answer'       => '-', 
                            'score'        => 0, // Soal kosong mutlak bernilai 0
                            'teacher_note' => $request->notes[$inputKey] ?? 'Jawaban kosong otomatis diberi nilai 0.',
                            'is_graded'    => true,
                            'is_finished'  => true
                        ]
                    );
                } else {
                    // Jika key berupa ID Integer murni (Berarti data jawaban lama siswa dari HP)
                    if (is_numeric($inputKey)) {
                        StudentAnswer::where('id', $inputKey)->update([
                            'score'        => $score,
                            'teacher_note' => $request->notes[$inputKey] ?? null,
                            'is_graded'    => true
                        ]);
                    }
                }
            }
        }

        return redirect()->route('guru.koreksi.index', $schedule_id)
                         ->with('success', 'Penilaian sukses disinkronkan dan disimpan!');
    }
    /**
     * 6. Export Nilai ke Excel
     */
    public function exportExcel(Request $request, $schedule_id)
    {
        $schedule = Schedule::findOrFail($schedule_id);
        
        $bobotPg = $schedule->weight_pg ?? 60;
        $bobotEssay = $schedule->weight_essay ?? 40;

        $namaFile = 'Nilai_' . str_replace(' ', '_', $schedule->subject->nama_mapel) . '_' . date('Ymd') . '.xlsx';

        return Excel::download(
            new NilaiUjianExport($schedule_id, $bobotPg, $bobotEssay), 
            $namaFile
        );
    }
}