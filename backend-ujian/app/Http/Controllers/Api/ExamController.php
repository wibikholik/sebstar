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

    public function getSoal(Request $request, $id)
    {
        $token = $request->header('X-Exam-Token');
        $schedule = Schedule::find($id);

        if (!$schedule || $schedule->token !== $token) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $questions = Question::where('subject_id', $schedule->subject_id)
            ->select('id', 'type', 'question_text', 'question_image', 'option_a', 'option_b', 'option_c', 'option_d', 'option_e')
            ->get();

        if ($questions->isEmpty()) {
            return response()->json(['message' => 'Soal belum tersedia'], 404);
        }

        return response()->json($questions, 200);
    }

    public function submitAnswer(Request $request, $id)
    {
        $request->validate([
            'question_id' => 'required|integer',
            'answer' => 'required|string',
        ]);

        $token = $request->header('X-Exam-Token');
        $schedule = Schedule::find($id);

        if (!$schedule || $schedule->token !== $token) {
            return response()->json(['message' => 'Sesi ujian tidak valid'], 403);
        }

        StudentAnswer::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'schedule_id' => $id,
                'question_id' => $request->question_id,
            ],
            ['answer' => $request->answer]
        );

        return response()->json(['message' => 'Jawaban tersimpan'], 200);
    }

    public function finishExam(Request $request, $id)
    {
        $token = $request->header('X-Exam-Token');
        $schedule = Schedule::find($id);

        if (!$schedule || $schedule->token !== $token) {
            return response()->json(['message' => 'Token tidak valid'], 403);
        }

        StudentAnswer::where('user_id', Auth::id())
                     ->where('schedule_id', $id)
                     ->update(['is_finished' => true]);

        return response()->json(['message' => 'Ujian berhasil dikirim'], 200);
    }

    /**
     * 1. Menampilkan Hasil Ujian dengan Bobot Dinamis
     */
    public function getResult(Request $request, $id)
    {
        $token = $request->header('X-Exam-Token');
        $schedule = Schedule::with('subject')->find($id);

        if (!$schedule || $schedule->token !== $token) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $userAnswers = StudentAnswer::with('question')
                                    ->where('schedule_id', $id)
                                    ->where('user_id', Auth::id())
                                    ->get();

        // Pisahkan jawaban PG dan Essay
        $pgAnswers = $userAnswers->filter(fn($a) => $a->question->type === 'pg');
        $essayAnswers = $userAnswers->filter(fn($a) => $a->question->type === 'essay');

        // Hitung PG (Skala 100)
        $totalPg = Question::where('subject_id', $schedule->subject_id)->where('type', 'pg')->count() ?: 1;
        $pgCorrect = 0;
        foreach ($pgAnswers as $ans) {
            if (strtolower(trim($ans->answer)) === strtolower(trim($ans->question->correct_answer))) {
                $pgCorrect++;
            }
        }
        $scorePgRaw = ($pgCorrect / $totalPg) * 100;

        // Hitung Essay (Skala 100) - Diambil dari kolom 'score' yang diisi guru
        $scoreEssayRaw = $essayAnswers->sum('score');

        // Ambil Bobot dari Database
        $wPg = ($schedule->weight_pg ?? 60) / 100;
        $wEssay = ($schedule->weight_essay ?? 40) / 100;

        // Kalkulasi Final
        $finalScore = ($scorePgRaw * $wPg) + ($scoreEssayRaw * $wEssay);
        
        // Cek apakah guru sudah selesai menilai essay
        $isGradedAll = $essayAnswers->isEmpty() ? true : !$essayAnswers->where('is_graded', false)->count();

        return response()->json([
            'success' => true,
            'data' => [
                'subject_name' => $schedule->subject->nama_mapel ?? 'N/A',
                'tanggal_ujian' => \Carbon\Carbon::parse($schedule->tanggal_ujian)->translatedFormat('d F Y'),
                'final_score' => $isGradedAll ? round($finalScore, 2) : 0,
                'is_complete' => $isGradedAll,
                'breakdown' => [
                    'score_pg' => round($scorePgRaw, 2),
                    'score_essay' => round($scoreEssayRaw, 2),
                    'weight_pg' => ($wPg * 100) . '%',
                    'weight_essay' => ($wEssay * 100) . '%'
                ],
                'pg' => [
                    'correct' => $pgCorrect,
                    'wrong' => $totalPg - $pgCorrect,
                    'total' => $totalPg
                ],
                'essay' => [
                    'answered' => $essayAnswers->count(),
                    'total' => Question::where('subject_id', $schedule->subject_id)->where('type', 'essay')->count()
                ],
                'details' => $userAnswers->map(fn($item) => [
                    'question' => $item->question->question_text,
                    'score' => $item->score,
                    'teacher_note' => $item->teacher_note
                ])
            ]
        ], 200);
    }

    /**
     * 2. Riwayat Ujian dengan Perhitungan Bobot
     */
    public function getHistory(Request $request)
    {
        $userId = Auth::id();

        $history = Schedule::whereHas('studentAnswers', function ($query) use ($userId) {
            $query->where('user_id', $userId)
                  ->where('is_finished', true);
        })
        ->with(['subject'])
        ->latest()
        ->get()
        ->map(function ($schedule) use ($userId) {
            $userAnswers = StudentAnswer::with('question')
                                        ->where('schedule_id', $schedule->id)
                                        ->where('user_id', $userId)
                                        ->get();

            // Hitung skor akhir sesuai bobot untuk history
            $pgAnswers = $userAnswers->filter(fn($a) => $a->question->type === 'pg');
            $totalPg = Question::where('subject_id', $schedule->subject_id)->where('type', 'pg')->count() ?: 1;
            
            $pgCorrect = 0;
            foreach ($pgAnswers as $ans) {
                if (strtolower(trim($ans->answer)) === strtolower(trim($ans->question->correct_answer))) {
                    $pgCorrect++;
                }
            }
            $scorePgRaw = ($pgCorrect / $totalPg) * 100;

            $essayAnswers = $userAnswers->filter(fn($a) => $a->question->type === 'essay');
            $scoreEssayRaw = $essayAnswers->sum('score');

            $wPg = ($schedule->weight_pg ?? 60) / 100;
            $wEssay = ($schedule->weight_essay ?? 40) / 100;
            
            $finalScore = ($scorePgRaw * $wPg) + ($scoreEssayRaw * $wEssay);

            return [
                'id'            => $schedule->id,
                'token'         => $schedule->token,
                'nama_mapel'    => $schedule->subject->nama_mapel ?? 'N/A',
                'tanggal_ujian' => \Carbon\Carbon::parse($schedule->tanggal_ujian)->translatedFormat('d M Y'),
                'score'         => round($finalScore, 2)
            ];
        });

        return response()->json($history, 200);
    }
}