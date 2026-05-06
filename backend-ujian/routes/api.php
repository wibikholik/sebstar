<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\ScheduleController;

// --- 1. RUTE PUBLIK ---
Route::post('/login', [AuthController::class, 'login']);

// --- 2. RUTE TERLINDUNGI (Wajib Token) ---
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Jadwal
    Route::get('/jadwal', [ScheduleController::class, 'index']);
    
    // Ujian & Soal
    Route::post('/ujian/{id}/verify-token', [ExamController::class, 'verifyToken']);
    Route::get('/ujian/{id}/soal', [ExamController::class, 'getSoal']);
    Route::post('/ujian/{id}/submit-answer', [ExamController::class, 'submitAnswer']);
    
    // FIX: Perbaikan Route agar cocok dengan pemanggilan di Frontend
    Route::post('/ujian/{id}/finish', [ExamController::class, 'finishExam']); 
    
    // FIX: Route History yang sebelumnya tidak ada
    Route::get('/ujian/history', [ExamController::class, 'getHistory']);
    
    // Hasil
    Route::get('/ujian/{id}/hasil', [ExamController::class, 'getResult']);
    
});