<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question;
use App\Models\Schedule;
use App\Models\StudentAnswer;
use App\Models\ExamLog; 
use App\Events\ExamMonitoringEvent; 
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

        $userId = Auth::id();
        $isLocked = ExamLog::where('schedule_id', $id)
            ->where('user_id', $userId)
            ->whereIn('type', ['KELUAR_APLIKASI', 'keluar_aplikasi', 'FORCE_SUBMIT'])
            ->exists();

        if ($isLocked) {
            return response()->json(['message' => 'Akses ditolak! Sesi pengerjaan Anda telah dikunci.'], 403);
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

    $isLocked = ExamLog::where('schedule_id', $id)
        ->where('user_id', $userId)
        ->whereIn('type', ['KELUAR_APLIKASI', 'keluar_aplikasi', 'FORCE_SUBMIT'])
        ->exists();

    if ($isLocked) {
        return response()->json(['message' => 'Akses ditolak! Ujian Anda sudah dikunci.'], 403); 
    }

    // 🚀 PERBAIKAN DI SINI: Filter berdasarkan schedule_id, BUKAN subject_id
    $questions = Question::where('schedule_id', $id)
        ->select('id', 'type', 'question_text', 'question_image', 'option_a', 'option_b', 'option_c', 'option_d', 'option_e')
        ->inRandomOrder() // Opsional: Tambahkan ini jika ingin soalnya diacak untuk setiap siswa
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

        $isLocked = ExamLog::where('schedule_id', $id)
            ->where('user_id', $userId)
            ->whereIn('type', ['KELUAR_APLIKASI', 'keluar_aplikasi', 'FORCE_SUBMIT'])
            ->exists();

        if ($isLocked) {
            return response()->json(['message' => 'Gagal menyimpan. Ujian dibekukan.'], 403);
        }

        StudentAnswer::updateOrCreate(
            ['user_id' => $userId, 'schedule_id' => $id, 'question_id' => $request->question_id],
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
    $schedule = Schedule::with('subject')->findOrFail($id);
    $userId = Auth::id();

    // 1. Ambil data agregasi siswa secara presisi (Sama persis dengan logika Index Web)
    $studentData = \App\Models\User::where('id', $userId)
        ->withCount(['answers as total_essay' => function($q) use ($id) {
            $q->where('schedule_id', $id)
              ->whereHas('question', fn($query) => $query->where('type', 'essay'));
        }])
        ->withCount(['answers as graded_essay' => function($q) use ($id) {
            $q->where('schedule_id', $id)
              ->where('is_graded', true)
              ->whereHas('question', fn($query) => $query->where('type', 'essay'));
        }])
        ->withCount(['answers as total_pg' => function($q) use ($id) {
            $q->where('schedule_id', $id)
              ->whereHas('question', fn($query) => $query->where('type', 'pg'));
        }])
        ->withCount(['answers as benar_pg' => function($q) use ($id) {
            $q->where('schedule_id', $id)
              ->whereHas('question', function($query) {
                  $query->where('type', 'pg')
                        ->whereRaw('student_answers.answer = questions.correct_answer'); 
              });
        }])
        ->withAvg(['answers as avg_skor_essay' => function($q) use ($id) {
            $q->where('schedule_id', $id)
              ->where('is_graded', true)
              ->whereHas('question', fn($query) => $query->where('type', 'essay'));
        }], 'score')
        ->first();

    if (!$studentData) {
        return response()->json(['success' => false, 'message' => 'Data jawaban tidak ditemukan'], 404);
    }

    // 2. Kalkulasi Skor PG Murni (Sesuai Web)
    $scorePgMurni = ($studentData->total_pg > 0) ? ($studentData->benar_pg / $studentData->total_pg) * 100 : 0;

    // 3. Kalkulasi Skor Essay Murni (Sesuai Web - Tanpa dikali 10 sepihak)
    $scoreEssayMurni = $studentData->avg_skor_essay ?? 0;

    // 4. Ambil Bobot Jurnal Jadwal Ujian
    $bobotPg = $schedule->weight_pg ?? 60;
    $bobotEssay = $schedule->weight_essay ?? 40;

    // 5. Hitung Nilai Akhir
    $finalScore = ($scorePgMurni * ($bobotPg / 100)) + ($scoreEssayMurni * ($bobotEssay / 100));

    // 6. Indikator Selesai: Jika total soal essay > 0, maka jumlah yang dikoreksi harus sama dengan total soal
    // Jika tidak ada essay (hanya PG), otomatis dianggap true (complete)
    $isComplete = true;
    if ($studentData->total_essay > 0) {
        $isComplete = ($studentData->graded_essay == $studentData->total_essay);
    }

    return response()->json([
        'success' => true,
        'data' => [
            'subject_name' => $schedule->subject->nama_mapel ?? $schedule->subject->name,
            'is_complete'  => $isComplete,
            'final_score'  => round($finalScore, 2),
            'breakdown'    => [
                'score_pg'     => round($scorePgMurni, 2),
                'score_essay'  => round($scoreEssayMurni, 2),
                'weight_pg'    => $bobotPg, // Ditambahkan agar tidak undefined di mobile
                'weight_essay' => $bobotEssay // Ditambahkan agar tidak undefined di mobile
            ]
        ]
    ]);
}
    public function logPelanggaran(Request $request, $id)
    {
        $request->validate([
            'type' => 'required|string',
        ]);

        $log = ExamLog::create([
            'schedule_id' => $id, 
            'user_id' => Auth::id(),
            'type' => strtoupper($request->type), 
            'details' => $request->details ?? 'Pelanggaran fokus layar.',
        ]);

        broadcast(new ExamMonitoringEvent($id, Auth::id(), 'PELANGGARAN', "Siswa: {$request->type}"))->toOthers();

        return response()->json(['status' => 'success'], 200);
    }
}