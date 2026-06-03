<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Schedule;
use App\Imports\QuestionsImport; 
use Maatwebsite\Excel\Facades\Excel; 
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
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', // Mendukung tipe gambar modern webp
        ]);

        $schedule = Schedule::findOrFail($schedule_id);

        $data = [
            'subject_id'     => $schedule->subject_id,
            'schedule_id'    => $schedule->id,
            'user_id'        => Auth::id(),
            'type'           => $request->type,
            'question_text'  => $request->question_text,
            'correct_answer' => ($request->type == 'pg') ? strtoupper(trim($request->correct_answer_pg)) : null,
        ];

        // Memproses & Menyimpan File Gambar Soal ke folder public/storage/uploads/questions
        if ($request->hasFile('question_image')) {
            $file = $request->file('question_image');
            $path = $file->store('uploads/questions', 'public');
            $data['question_image'] = $path;
        }

        if ($request->type == 'pg') {
            foreach (['a', 'b', 'c', 'd', 'e'] as $opt) {
                $data["option_$opt"] = $request->{"option_$opt"};
            }
        }

        Question::create($data);

        return redirect()->back()->with('success', '✨ Soal baru berhasil ditambahkan ke bank soal!');
    }

    /**
     * 🚀 FITUR BARU: Memproses File Excel/CSV yang di-upload oleh Admin/Guru
     */
    public function importExcel(Request $request, $schedule_id)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:5120', // Maks file 5MB
        ]);

        try {
            // Panggil file QuestionsImport sambil melempar ID Jadwal aktif
            Excel::import(new QuestionsImport($schedule_id), $request->file('file_excel'));
            
            return redirect()->back()->with('success', '✅ Berhasil mengimpor bank soal massal ke jadwal ujian ini!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', '⚠ Gagal memproses berkas. Pastikan struktur kolom sesuai template! Info: ' . $e->getMessage());
        }
    }

    /**
     * 📥 FITUR BARU: Mengunduh format template CSV Manusiawi (Teks murni) tanpa kode ID membingungkan
     */
    public function downloadTemplate()
    {
        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=template_soal_sebstar.csv",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        // Header kolom wajib disesuaikan 100% dengan properti $fillable di model Question.php kamu
        $columns = ['type', 'question_text', 'option_a', 'option_b', 'option_c', 'option_d', 'option_e', 'correct_answer'];

        $callback = function() use($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            
            // Baris Baris Contoh Isian Template (Opsi sampai E, Kunci Kapital)
            fputcsv($file, ['pg', 'Berapakah hasil matematika dari operasi 10 + 5?', '12', '13', '14', '15', '16', 'D']);
            fputcsv($file, ['pg', 'Sistem operasi mobile open-source buatan Google adalah...', 'iOS', 'Android', 'Windows Phone', 'Linux Mint', 'Ubuntu Touch', 'B']);
            fputcsv($file, ['essay', 'Jelaskan dampak buruk terjadinya pencemaran sungai bagi ekosistem sekitar!', '', '', '', '', '', '']);
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
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
            'question_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data = [
            'type'           => $request->type,
            'question_text'  => $request->question_text,
            'correct_answer' => ($request->type == 'pg') ? strtoupper(trim($request->correct_answer_pg)) : null,
        ];

        // Mengamankan penimpaan file gambar: Gambar lama otomatis dihapus agar disk storage hemat
        if ($request->hasFile('question_image')) {
            if ($question->question_image && Storage::disk('public')->exists($question->question_image)) {
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
        
        // Bersihkan berkas fisik gambar di folder storage sebelum record database dihapus
        if ($question->question_image && Storage::disk('public')->exists($question->question_image)) {
            Storage::disk('public')->delete($question->question_image);
        }
        
        $question->delete();

        return redirect()->back()->with('success', 'Soal berhasil dihapus!');
    }
}