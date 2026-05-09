<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Schedule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class QuestionController extends Controller
{
    public function index($schedule_id)
    {
        $schedule = Schedule::with(['subject', 'classroom', 'examType'])->findOrFail($schedule_id);
        
        // Menampilkan semua soal yang terhubung ke jadwal ini
        $questions = Question::where('schedule_id', $schedule_id)
                            ->latest()
                            ->get();

        return view('admin.questions.index', compact('schedule', 'questions'));
    }

    public function store(Request $request, $schedule_id)
    {
        $request->validate([
            'type'           => 'required|in:pg,essay',
            'question_text'  => 'required',
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $schedule = Schedule::findOrFail($schedule_id);

        $data = [
            'subject_id'     => $schedule->subject_id,
            'schedule_id'    => $schedule->id,
            'user_id'        => Auth::id(),
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

        return redirect()->back()->with('success', 'Soal berhasil ditambahkan!');
    }

    public function copy(Request $request, $schedule_id)
    {
        $request->validate([
            'from_schedule_id' => 'required|exists:schedules,id'
        ]);

        $sourceQuestions = Question::where('schedule_id', $request->from_schedule_id)->get();

        if ($sourceQuestions->isEmpty()) {
            return redirect()->back()->with('error', 'Jadwal sumber tidak memiliki soal.');
        }

        $targetSchedule = Schedule::findOrFail($schedule_id);

        foreach ($sourceQuestions as $q) {
            $newQuestion = $q->replicate();
            $newQuestion->schedule_id = $targetSchedule->id;
            $newQuestion->user_id = Auth::id();
            $newQuestion->save();
        }

        return redirect()->back()->with('success', count($sourceQuestions) . ' soal berhasil disalin!');
    }

    public function update(Request $request, $schedule_id, $id)
    {
        $question = Question::findOrFail($id);

        $request->validate([
            'type'           => 'required|in:pg,essay',
            'question_text'  => 'required',
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

        return redirect()->back()->with('success', 'Soal berhasil diperbarui!');
    }

    public function destroy($schedule_id, $id)
    {
        $question = Question::findOrFail($id);
        if ($question->question_image) {
            Storage::disk('public')->delete($question->question_image);
        }
        $question->delete();

        return redirect()->back()->with('success', 'Soal berhasil dihapus!');
    }
}