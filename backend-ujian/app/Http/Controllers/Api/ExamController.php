<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Schedule;
use App\Models\StudentAnswer;
use App\Models\ExamLog; 
use App\Events\ExamMonitoringEvent; // 📢 Broadcast Event Reverb Real-time
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ExamController extends Controller
{
    public function verifyToken(Request $request, $id)
    {
        $request->validate(['token' => 'required']);
        $schedule = Schedule::find($id);

        if (!$schedule) {
            return response()->json(['message' => 'Ujian tidak ditemukan'], 404);
        }

        // 🛑 Cegah login jika siswa berstatus Diskualifikasi atau di-Force Submit
        $userId = Auth::id();
        $isLocked = ExamLog::where('schedule_id', $id)
            ->where('user_id', $userId)
            ->whereIn('type', ['KELUAR_APLIKASI', 'keluar_aplikasi', 'FORCE_SUBMIT'])
            ->exists();

        if ($isLocked) {
            return response()->json(['message' => 'Akses ditolak! Sesi pengerjaan Anda telah dikunci oleh sistem.'], 403);
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
        $userId = Auth::id();

        if (!$schedule || $schedule->token !== $token) {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }

        // 🛑 Blokir total pengambilan soal jika siswa sedang didiskualifikasi / force submit
        $isLocked = ExamLog::where('schedule_id', $id)
            ->where('user_id', $userId)
            ->whereIn('type', ['KELUAR_APLIKASI', 'keluar_aplikasi', 'FORCE_SUBMIT'])
            ->exists();

        if ($isLocked) {
            return response()->json([
                'message' => 'Akses ditolak! Lembar pengerjaan ujian Anda sudah dikunci akibat pelanggaran fokus layar atau penghentian paksa oleh pengawas.'
            ], 403); 
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
        $userId = Auth::id();

        if (!$schedule || $schedule->token !== $token) {
            return response()->json(['message' => 'Sesi ujian tidak valid'], 403);
        }

        // 🛑 Blokir auto-save jawaban jika statusnya sudah dikunci
        $isLocked = ExamLog::where('schedule_id', $id)
            ->where('user_id', $userId)
            ->whereIn('type', ['KELUAR_APLIKASI', 'keluar_aplikasi', 'FORCE_SUBMIT'])
            ->exists();

        if ($isLocked) {
            return response()->json(['message' => 'Gagal menyimpan. Status ujian Anda telah dibekukan.'], 403);
        }

        StudentAnswer::updateOrCreate(
            [
                'user_id' => $userId,
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

        $pgAnswers = $userAnswers->filter(fn($a) => $a->question && $a->question->type === 'pg');
        $essayAnswers = $userAnswers->filter(fn($a) => $a->question && $a->question->type === 'essay');

        $totalPg = Question::where('subject_id', $schedule->subject_id)->where('type', 'pg')->count() ?: 1;
        $pgCorrect = 0;
        foreach ($pgAnswers as $ans) {
            if (strtolower(trim($ans->answer)) === strtolower(trim($ans->question->correct_answer))) {
                $pgCorrect++;
            }
        }
        $scorePgRaw = ($pgCorrect / $totalPg) * 100;

        // 📢 FIX UTAMA SINKRONISASI: Menggunakan avg() rata-rata agar berskala 0-100 kembali
        $scoreEssayRaw = $essayAnswers->count() > 0 ? $essayAnswers->where('is_graded', true)->avg('score') : 0;

        $wPg = ($schedule->weight_pg ?? 60) / 100;
        $wEssay = ($schedule->weight_essay ?? 40) / 100;

        $finalScore = ($scorePgRaw * $wPg) + ($scoreEssayRaw * $wEssay);
        
        // Pengecekan apakah seluruh lembar esai milik siswa ini sudah diberi nilai oleh guru pengampu
        $isGradedAll = $essayAnswers->isEmpty() ? true : !$essayAnswers->where('is_graded', false)->count();

        return response()->json([
            'success' => true,
            'data' => [
                'subject_name' => $schedule->subject->nama_mapel ?? 'N/A',
                'tanggal_ujian' => \Carbon\Carbon::parse($schedule->tanggal_ujian)->translatedFormat('d F Y'),
                'final_score' => $isGradedAll ? round($finalScore, 2) : 0, // Hanya publish nilai jika koreksi esai 100% beres
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
                    'question' => $item->question->question_text ?? '',
                    'score' => $item->score,
                    'teacher_note' => $item->teacher_note
                ])
            ]
        ], 200);
    }

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

            $pgAnswers = $userAnswers->filter(fn($a) => $a->question && $a->question->type === 'pg');
            $totalPg = Question::where('subject_id', $schedule->subject_id)->where('type', 'pg')->count() ?: 1;
            
            $pgCorrect = 0;
            foreach ($pgAnswers as $ans) {
                if (strtolower(trim($ans->answer)) === strtolower(trim($ans->question->correct_answer))) {
                    $pgCorrect++;
                }
            }
            $scorePgRaw = ($pgCorrect / $totalPg) * 100;

            $essayAnswers = $userAnswers->filter(fn($a) => $a->question && $a->question->type === 'essay');
            
            // 📢 FIX UTAMA SINKRONISASI: Menggunakan avg() rata-rata untuk riwayat nilai
            $scoreEssayRaw = $essayAnswers->count() > 0 ? $essayAnswers->where('is_graded', true)->avg('score') : 0;

            $wPg = ($schedule->weight_pg ?? 60) / 100;
            $wEssay = ($schedule->weight_essay ?? 40) / 100;
            
            $finalScore = ($scorePgRaw * $wPg) + ($scoreEssayRaw * $wEssay);

            // Cek status apakah nilai esai sudah siap dipublish total ke rapor mobile siswa
            $isGradedAll = $essayAnswers->isEmpty() ? true : !$essayAnswers->where('is_graded', false)->count();

            return [
                'id'            => $schedule->id,
                'token'         => $schedule->token,
                'nama_mapel'    => $schedule->subject->nama_mapel ?? 'N/A',
                'tanggal_ujian' => \Carbon\Carbon::parse($schedule->tanggal_ujian)->translatedFormat('d M Y'),
                'score'         => $isGradedAll ? round($finalScore, 2) : 0 // Tetap tampilkan 0 di history jika guru belum beres mengoreksi
            ];
        ]);

        return response()->json($history, 200);
    }

    public function logPelanggaran(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|string',
            'details' => 'nullable|string'
        ]);

        try {
            $userId = Auth::id(); 

            $log = ExamLog::create([
                'schedule_id' => $id, 
                'user_id' => $userId,
                'type' => strtoupper($request->type), 
                'details' => $request->details ?? 'Siswa terdeteksi memindahkan fokus layar ujian browser ketat.',
                'created_at' => Carbon::now('Asia/Jakarta')
            ]);

            broadcast(new ExamMonitoringEvent($id, $userId, 'PELANGGARAN', "Siswa terdeteksi melakukan tindakan: {$request->type}."))->toOthers();

            return response()->json([
                'status' => 'success',
                'message' => 'Log pelanggaran sukses dikirim dan disiarkan secara real-time ke web pengawas.',
                'data' => $log
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengirimkan log pengawasan ke database server.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}