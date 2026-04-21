<?php



use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ExamController;
use App\Http\Controllers\Api\ScheduleController;

// --- 1. RUTE PUBLIK ---
// Siswa tidak perlu token untuk login
Route::post('/login', [AuthController::class, 'login']);

// --- 2. RUTE TERLINDUNGI (Wajib Token) ---
// Gunakan middleware 'auth:sanctum' sebagai satpam
Route::middleware('auth:sanctum')->group(function () {
    
    // Auth: Mengambil profil & logout
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Jadwal: Mengambil daftar ujian yang aktif
    Route::get('/jadwal', [ScheduleController::class, 'index']);
    
    // Ujian: Mengambil soal
    Route::get('/ambil-soal/{exam_id}', [ExamController::class, 'getQuestions']);
    
});