<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Schedule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    public function index()
    {
        return redirect()->route('guru.schedules.index');
    }

    public function manage($schedule_id)
    {
        $schedule = Schedule::with(['subject', 'classroom', 'examType'])->findOrFail($schedule_id);
        $questions = Question::where('schedule_id', $schedule_id)->latest()->get();
        $teacherId = Auth::id();

        // Optimasi: Memastikan pencarian teacher_ids fleksibel terhadap format data
        $otherSchedules = Schedule::with(['classroom', 'subject'])
            ->where('id', '!=', $schedule_id)
            ->where('subject_id', $schedule->subject_id)
            ->where(function($query) use ($teacherId) {
                $query->where('created_by', $teacherId)
                      ->orWhereRaw('JSON_CONTAINS(teacher_ids, ?)', [(string)$teacherId])
                      ->orWhere('teacher_ids', 'LIKE', "%$teacherId%");
            })
            ->has('questions')
            ->get();

        return view('guru.questions.manage', compact('schedule', 'questions', 'otherSchedules'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'schedule_id'    => 'required|exists:schedules,id',
            'subject_id'     => 'required',
            'type'           => 'required|in:pg,essay',
            'question_text'  => 'required',
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            // Validasi opsi wajib jika PG
            'option_a'       => 'required_if:type,pg',
            'option_b'       => 'required_if:type,pg',
            'correct_answer_pg' => 'required_if:type,pg',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->only(['subject_id', 'schedule_id', 'type', 'question_text']);
            $data['user_id'] = Auth::id();
            $data['correct_answer'] = ($request->type == 'pg') ? $request->correct_answer_pg : $request->correct_answer_essay;

            if ($request->hasFile('question_image')) {
                $data['question_image'] = $request->file('question_image')->store('uploads/questions', 'public');
            }

            if ($request->type == 'pg') {
                foreach (['a', 'b', 'c', 'd', 'e'] as $opt) {
                    $data["option_$opt"] = $request->{"option_$opt"};
                }
            }

            Question::create($data);
            DB::commit();

            return redirect()->route('guru.questions.manage', $request->schedule_id)
                             ->with('success', 'Soal berhasil ditambahkan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
        $question = Question::findOrFail($id);
        
        $request->validate([
            'question_text'  => 'required',
            'type'           => 'required|in:pg,essay',
        ]);

        $data = $request->only(['type', 'question_text']);
        $data['correct_answer'] = ($request->type == 'pg') ? $request->correct_answer_pg : $request->correct_answer_essay;

        if ($request->hasFile('question_image')) {
            if ($question->question_image) Storage::disk('public')->delete($question->question_image);
            $data['question_image'] = $request->file('question_image')->store('uploads/questions', 'public');
        }

        // Logic reset/update opsi
        foreach (['a', 'b', 'c', 'd', 'e'] as $opt) {
            $data["option_$opt"] = ($request->type == 'pg') ? $request->{"option_$opt"} : null;
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