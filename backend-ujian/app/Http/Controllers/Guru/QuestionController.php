<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Schedule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    /**
     * Index dialihkan ke jadwal utama
     */
    public function index()
    {
        return redirect()->route('guru.schedules.index');
    }

    /**
     * Menampilkan halaman kelola soal
     */
    public function manage($schedule_id)
    {
        // 1. Ambil data jadwal aktif
        $schedule = Schedule::with(['subject', 'classroom', 'examType'])->findOrFail($schedule_id);

        // 2. Ambil semua soal yang sudah ada di jadwal ini
        $questions = Question::where('schedule_id', $schedule_id)
                             ->latest()
                             ->get();

        $teacherId = Auth::id();

        // 3. LOGIC SALIN SOAL:
        // Ambil jadwal LAIN yang Mapelnya SAMA dan kamu adalah Gurunya
        $otherSchedules = Schedule::with(['classroom', 'subject'])
            ->where('id', '!=', $schedule_id) // Bukan jadwal yang sedang dibuka
            ->where('subject_id', $schedule->subject_id) // WAJIB MAPEL YANG SAMA
            ->where(function($query) use ($teacherId) {
                $query->where('created_by', $teacherId) // Jadwal buatan sendiri
                      ->orWhereJsonContains('teacher_ids', (string)$teacherId) // Jadwal titipan pusat (JSON)
                      ->orWhere('teacher_ids', $teacherId); // Backup format string/int
            })
            ->has('questions') // HANYA ambil yang sudah ada soalnya
            ->get();

        return view('guru.questions.manage', compact('schedule', 'questions', 'otherSchedules'));
    }

    /**
     * Simpan Soal Baru (PG atau Essay)
     */
    public function store(Request $request)
    {
        $request->validate([
            'subject_id'     => 'required',
            'schedule_id'    => 'required',
            'type'           => 'required|in:pg,essay',
            'question_text'  => 'required',
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = [
            'subject_id'     => $request->subject_id,
            'schedule_id'    => $request->schedule_id,
            'user_id'        => Auth::id(),
            'type'           => $request->type,
            'question_text'  => $request->question_text,
            'correct_answer' => ($request->type == 'pg') ? $request->correct_answer_pg : $request->correct_answer_essay,
        ];

        // Handle Upload Gambar
        if ($request->hasFile('question_image')) {
            $data['question_image'] = $request->file('question_image')->store('uploads/questions', 'public');
        }

        // Handle Opsi PG
        if ($request->type == 'pg') {
            foreach (['a', 'b', 'c', 'd', 'e'] as $opt) {
                $data["option_$opt"] = $request->{"option_$opt"};
            }
        }

        Question::create($data);

        return redirect()->route('guru.questions.manage', $request->schedule_id)
                         ->with('success', 'Soal berhasil ditambahkan!');
    }

    /**
     * Update Soal yang sudah ada
     */
    public function update(Request $request, $id)
    {
        $question = Question::findOrFail($id);

        $request->validate([
            'schedule_id'    => 'required',
            'type'           => 'required|in:pg,essay',
            'question_text'  => 'required',
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = [
            'type'           => $request->type,
            'question_text'  => $request->question_text,
            'correct_answer' => ($request->type == 'pg') ? $request->correct_answer_pg : $request->correct_answer_essay,
        ];

        // Update Gambar jika ada file baru
        if ($request->hasFile('question_image')) {
            if ($question->question_image) {
                Storage::disk('public')->delete($question->question_image);
            }
            $data['question_image'] = $request->file('question_image')->store('uploads/questions', 'public');
        }

        // Mapping Opsi PG atau Reset jika ganti ke Essay
        if ($request->type == 'pg') {
            foreach (['a', 'b', 'c', 'd', 'e'] as $opt) {
                $data["option_$opt"] = $request->{"option_$opt"};
            }
        } else {
            foreach (['a', 'b', 'c', 'd', 'e'] as $opt) {
                $data["option_$opt"] = null;
            }
        }

        $question->update($data);

        return redirect()->route('guru.questions.manage', $request->schedule_id)
                         ->with('success', 'Soal berhasil diperbarui!');
    }

    /**
     * Copy Soal dari Jadwal Sumber ke Jadwal Target
     */
    public function copy(Request $request, $schedule_id)
    {
        $request->validate([
            'from_schedule_id' => 'required|exists:schedules,id'
        ]);

        // Ambil soal dari jadwal sumber
        $sourceQuestions = Question::where('schedule_id', $request->from_schedule_id)->get();

        if ($sourceQuestions->isEmpty()) {
            return redirect()->back()->with('error', 'Jadwal sumber ternyata tidak memiliki soal.');
        }

        $targetSchedule = Schedule::findOrFail($schedule_id);

        // Duplikasi soal menggunakan replicate()
        foreach ($sourceQuestions as $q) {
            $newQuestion = $q->replicate();
            $newQuestion->schedule_id = $targetSchedule->id;
            $newQuestion->subject_id = $targetSchedule->subject_id;
            $newQuestion->user_id = Auth::id(); // Penanda siapa yang menyalin
            $newQuestion->save();
        }

        return redirect()->back()->with('success', count($sourceQuestions) . ' soal berhasil disalin ke jadwal ini!');
    }

    /**
     * Hapus Soal secara permanen
     */
    public function destroy($id)
    {
        $question = Question::findOrFail($id);

        // Hapus file gambar dari storage jika ada
        if ($question->question_image) {
            Storage::disk('public')->delete($question->question_image);
        }

        $question->delete();

        return redirect()->back()->with('success', 'Soal berhasil dihapus!');
    }
}