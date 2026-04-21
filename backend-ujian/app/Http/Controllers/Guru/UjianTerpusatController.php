<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Schedule;
use Illuminate\Support\Facades\Storage;

class UjianTerpusatController extends Controller
{
    public function index()
    {
        $teacherId = auth()->id();

        $schedules = Schedule::with(['subject', 'classroom'])
            ->where(function($query) use ($teacherId) {
                $query->whereJsonContains('teacher_ids', (string)$teacherId)
                      ->orWhere('teacher_ids', $teacherId);
            })
            ->latest()
            ->get();

        return view('guru.ujian_terpusat.index', compact('schedules'));
    }

    public function manage($schedule_id)
    {
        $schedule = Schedule::with(['subject', 'classroom'])->findOrFail($schedule_id);

        $questions = Question::where('subject_id', $schedule->subject_id)
                             ->where('user_id', auth()->id())
                             ->latest()
                             ->get();

        return view('guru.ujian_terpusat.manage', compact('schedule', 'questions'));
    }

    public function create(Request $request)
    {
        $subject_id = $request->subject_id;
        $schedule_id = $request->schedule_id;

        return view('guru.ujian_terpusat.create', compact('subject_id', 'schedule_id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required',
            'type' => 'required|in:pg,essay',
            'question_text' => 'required',
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = [
            'subject_id'     => $request->subject_id,
            'user_id'        => auth()->id(),
            'type'           => $request->type,
            'question_text'  => $request->question_text,
            'correct_answer' => ($request->type == 'pg') ? $request->correct_answer_pg : $request->correct_answer_essay,
        ];

        if ($request->hasFile('question_image')) {
            $data['question_image'] = $request->file('question_image')->store('uploads/questions', 'public');
        }

        if ($request->type == 'pg') {
            foreach (['a', 'b', 'c', 'd', 'e'] as $opt) {
                $data["option_$opt"] = $request->{"option_$opt"};
            }
        }

        Question::create($data);

        return redirect()->route('guru.ujian-terpusat.manage', $request->schedule_id)
                         ->with('success', 'Soal berhasil ditambahkan!');
    }

    public function edit(Request $request, $id)
    {
        $question = Question::findOrFail($id);
        // Tangkap schedule_id dari URL agar saat update bisa kembali ke halaman manage
        $schedule_id = $request->schedule_id;
        
        return view('guru.ujian_terpusat.edit', compact('question', 'schedule_id'));
    }

    public function update(Request $request, $id)
    {
        $question = Question::findOrFail($id);

        $request->validate([
            'type' => 'required|in:pg,essay',
            'question_text' => 'required',
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = [
            'type'           => $request->type,
            'question_text'  => $request->question_text,
            'correct_answer' => ($request->type == 'pg') ? $request->correct_answer_pg : $request->correct_answer_essay,
        ];

        if ($request->hasFile('question_image')) {
            if ($question->question_image) {
                Storage::disk('public')->delete($question->question_image);
            }
            $data['question_image'] = $request->file('question_image')->store('uploads/questions', 'public');
        }

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

        // Redirect ke halaman manage berdasarkan schedule_id yang dikirim dari form edit
      if ($request->has('schedule_id') && $request->schedule_id != null) {
        return redirect()->route('guru.ujian-terpusat.manage', $request->schedule_id)
                         ->with('success', 'Soal berhasil diperbarui!');
    }

    // Jika schedule_id tidak ada, paksa kembali ke index jadwal daripada stuck di edit
    return redirect()->route('guru.ujian-terpusat.index')
                     ->with('success', 'Soal diperbarui, kembali ke daftar jadwal.');
    }

    public function destroy($id)
    {
        $question = Question::findOrFail($id);

        if ($question->question_image) {
            Storage::disk('public')->delete($question->question_image);
        }

        $question->delete();

        return redirect()->back()->with('success', 'Soal berhasil dihapus!');
    }
}