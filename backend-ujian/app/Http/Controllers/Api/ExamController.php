<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Schedule;
use App\Models\StudentAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    /**
     * Verifikasi Token Ujian
     */
    public function verifyToken(Request $request, $id)
    {
        $request->validate(['token' => 'required']);
        $schedule = Schedule::find($id);

        if (!$schedule) {
            return response()->json(['message' => 'Ujian tidak ditemukan'], 404);
        }

        if ($schedule->token !== $request->token) {
            return response()->json(['message' => 'Token ujian salah'], 403);
        }

        return response()->json(['message' => 'Token valid'], 200);
    }

    /**
     * Ambil Soal Ujian (PENTING: 'type' sudah dimasukkan di select)
     */
    public function getSoal(Request $request, $id)
    {
        $token = $request->header('X-Exam-Token');
        $schedule = Schedule::find($id);

        if (!$schedule || $schedule->token !== $token) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        // PERBAIKAN: Menambahkan 'type' ke select agar frontend tahu ini soal PG atau Essay
        $questions = Question::where('subject_id', $schedule->subject_id)
            ->select('id', 'type', 'question_text', 'question_image', 'option_a', 'option_b', 'option_c', 'option_d', 'option_e')
            ->get();

        if ($questions->isEmpty()) {
            return response()->json(['message' => 'Soal belum tersedia'], 404);
        }

        return response()->json($questions, 200);
    }

    /**
     * Simpan Jawaban (PG & Essay)
     */
    public function submitAnswer(Request $request, $id)
    {
        // Validasi input
        $request->validate([
            'question_id' => 'required|integer',
            'answer' => 'required|string',
        ]);

        $token = $request->header('X-Exam-Token');
        $schedule = Schedule::find($id);

        if (!$schedule || $schedule->token !== $token) {
            return response()->json(['message' => 'Sesi ujian tidak valid'], 403);
        }

        // Simpan ke database
        StudentAnswer::updateOrCreate(
            [
                'user_id' => Auth::id(), // Pastikan siswa login
                'schedule_id' => $id,
                'question_id' => $request->question_id,
            ],
            ['answer' => $request->answer]
        );

        return response()->json(['message' => 'Jawaban tersimpan'], 200);
    }

    /**
     * Mengakhiri Ujian
     */
    public function finishExam(Request $request, $id)
{
    $token = $request->header('X-Exam-Token');
    $schedule = Schedule::find($id);

    if (!$schedule || $schedule->token !== $token) {
        return response()->json(['message' => 'Token tidak valid'], 403);
    }

    // Update semua jawaban user ini untuk schedule ini menjadi selesai
    StudentAnswer::where('user_id', Auth::id())
                 ->where('schedule_id', $id)
                 ->update(['is_finished' => true]);

    return response()->json(['message' => 'Ujian berhasil dikirim'], 200);
}
    /**
     * Mengambil Hasil Ujian
     */
   public function getResult(Request $request, $id)
{
    $token = $request->header('X-Exam-Token');
    $schedule = Schedule::find($id);

    if (!$schedule || $schedule->token !== $token) {
        return response()->json(['message' => 'Akses ditolak'], 403);
    }

    $questions = Question::where('subject_id', $schedule->subject_id)->get();
    $userAnswers = StudentAnswer::where('schedule_id', $id)
                                ->where('user_id', Auth::id())
                                ->get();

    $pgCorrect = 0;
    $pgWrong = 0;
    $essayAnswered = 0;
    
    // Hitung total untuk label
    $totalPg = $questions->where('type', 'pg')->count();
    $totalEssay = $questions->where('type', 'essay')->count();

    foreach ($questions as $question) {
        $answer = $userAnswers->where('question_id', $question->id)->first();

        if ($question->type === 'pg') {
            if ($answer && strtolower($answer->answer) === strtolower($question->correct_answer)) {
                $pgCorrect++;
            } else {
                $pgWrong++;
            }
        } elseif ($question->type === 'essay') {
            if ($answer && !empty($answer->answer)) {
                $essayAnswered++;
            }
        }
    }

    $score = ($totalPg > 0) ? ($pgCorrect / $totalPg) * 100 : 0;

    return response()->json([
        'score' => round($score, 2),
        'pg' => [
            'correct' => $pgCorrect,
            'wrong' => $pgWrong,
            'total' => $totalPg
        ],
        'essay' => [
            'answered' => $essayAnswered,
            'total' => $totalEssay
        ]
    ], 200);
}
/**
 * Mengambil Riwayat Nilai
 */
/**
 * Mengambil Riwayat Nilai
 */
public function getHistory(Request $request)
{
    $userId = Auth::id();

    // 1. Ambil jadwal yang sudah dikerjakan (is_finished = true)
    $history = Schedule::whereHas('studentAnswers', function ($query) use ($userId) {
        $query->where('user_id', $userId)
              ->where('is_finished', true);
    })
    ->with(['subject', 'studentAnswers' => function($query) use ($userId) {
        $query->where('user_id', $userId);
    }])
    ->latest()
    ->get()
    ->map(function ($schedule) {
        // 2. Hitung ulang score PG agar bisa ditampilkan di history
        $questions = \App\Models\Question::where('subject_id', $schedule->subject_id)->get();
        $userAnswers = $schedule->studentAnswers;

        $pgCorrect = 0;
        $totalPg = $questions->where('type', 'pg')->count();

        foreach ($questions->where('type', 'pg') as $question) {
            $ans = $userAnswers->where('question_id', $question->id)->first();
            if ($ans && strtolower($ans->answer) === strtolower($question->correct_answer)) {
                $pgCorrect++;
            }
        }

        $score = ($totalPg > 0) ? ($pgCorrect / $totalPg) * 100 : 0;

        // 3. Kembalikan data yang sudah diformat untuk frontend
        return [
            'id' => $schedule->id,
            'token' => $schedule->token,
            'nama_mapel' => $schedule->subject->nama_mapel ?? 'N/A',
            'tanggal_ujian' => $schedule->tanggal_ujian,
            'score' => round($score, 2)
        ];
    });

    return response()->json($history, 200);
}
}