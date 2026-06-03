<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Schedule;
use App\Imports\QuestionsImport; // 🌟 Logic Import
use Maatwebsite\Excel\Facades\Excel; // 🌟 Facade Excel
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

    // 🚀 FITUR: Import Soal via Excel
    public function importExcel(Request $request, $schedule_id)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        try {
            Excel::import(new QuestionsImport($schedule_id), $request->file('file_excel'));
            return redirect()->back()->with('success', '✅ Soal berhasil diimpor!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', '⚠ Gagal import: ' . $e->getMessage());
        }
    }

    // 📥 FITUR: Download Template Excel Soal
    public function downloadTemplate()
    {
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=template_soal_sebstar.csv",
        ];

        $columns = ['type', 'question_text', 'option_a', 'option_b', 'option_c', 'option_d', 'option_e', 'correct_answer'];

        $callback = function() use($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            fputcsv($file, ['pg', 'Contoh pertanyaan?', 'Opsi A', 'Opsi B', 'Opsi C', 'Opsi D', 'Opsi E', 'A']);
            fputcsv($file, ['essay', 'Contoh soal essay?', '', '', '', '', '', '']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function store(Request $request)
    {
        $request->validate([
            'schedule_id'    => 'required|exists:schedules,id',
            'type'           => 'required|in:pg,essay',
            'question_text'  => 'required',
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        try {
            DB::beginTransaction();
            $schedule = Schedule::findOrFail($request->schedule_id);

            $data = $request->only(['type', 'question_text']);
            $data['subject_id'] = $schedule->subject_id;
            $data['schedule_id'] = $schedule->id;
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
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    // 🛠️ PERBAIKAN: Parameter $schedule_id dihapus karena route hanya mengirim $id
    public function update(Request $request, $id)
    {
        $question = Question::findOrFail($id);
        
        $request->validate([
            'type'           => 'required|in:pg,essay',
            'question_text'  => 'required',
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $data = $request->only(['type', 'question_text']);
        $data['correct_answer'] = ($request->type == 'pg') ? $request->correct_answer_pg : $request->correct_answer_essay;

        if ($request->hasFile('question_image')) {
            if ($question->question_image) Storage::disk('public')->delete($question->question_image);
            $data['question_image'] = $request->file('question_image')->store('uploads/questions', 'public');
        }

        foreach (['a', 'b', 'c', 'd', 'e'] as $opt) {
            $data["option_$opt"] = ($request->type == 'pg') ? $request->{"option_$opt"} : null;
        }

        $question->update($data);

        // 🛠️ PERBAIKAN: Ambil schedule_id langsung dari model relasinya
        return redirect()->route('guru.questions.manage', $question->schedule_id)
                         ->with('success', 'Soal berhasil diperbarui!');
    }

    public function copy(Request $request, $schedule_id)
    {
        $request->validate(['from_schedule_id' => 'required|exists:schedules,id']);

        $sourceQuestions = Question::where('schedule_id', $request->from_schedule_id)->get();
        if ($sourceQuestions->isEmpty()) return back()->with('error', 'Jadwal sumber kosong.');

        $targetSchedule = Schedule::findOrFail($schedule_id);

        foreach ($sourceQuestions as $q) {
            $new = $q->replicate();
            $new->schedule_id = $targetSchedule->id;
            $new->subject_id = $targetSchedule->subject_id;
            $new->user_id = Auth::id();
            $new->save();
        }

        return back()->with('success', count($sourceQuestions) . ' soal berhasil disalin!');
    }

    // 🛠️ PERBAIKAN: Parameter $schedule_id juga dihapus untuk hapus soal
    public function destroy($id)
    {
        $question = Question::findOrFail($id);
        if ($question->question_image) Storage::disk('public')->delete($question->question_image);
        $question->delete();

        return back()->with('success', 'Soal berhasil dihapus!');
    }
}