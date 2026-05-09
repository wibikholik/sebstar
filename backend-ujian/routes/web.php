<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController as AdminDash;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\ClassroomController;
use App\Http\Controllers\Admin\MajorController;
use App\Http\Controllers\Admin\ScheduleController;

// Guru Controllers
use App\Http\Controllers\Guru\DashboardController as GuruDash;
use App\Http\Controllers\Guru\UjianTerpusatController;
use App\Http\Controllers\Guru\MonitoringController as GuruMonitor;

// Pengawas Controllers
use App\Http\Controllers\Pengawas\DashboardController as PengawasDash;
use App\Http\Controllers\Pengawas\MonitoringController as PengawasMonitor;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return redirect()->route('login');
});

// Pintu Masuk (Auth)
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// --- GROUP ADMIN ---
// --- GROUP ADMIN ---
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminDash::class, 'index'])->name('admin.dashboard');

    // Manajemen Data Master
    Route::resource('users', UserController::class)->names('admin.users');
    Route::resource('subjects', SubjectController::class)->names('admin.subjects');
    Route::resource('classrooms', ClassroomController::class)->names('admin.classrooms');
    Route::resource('majors', MajorController::class)->names('admin.majors');
    
    // Penjadwalan & Token
    Route::resource('schedules', ScheduleController::class)->names('admin.schedules');
    Route::post('schedules/{id}/status', [ScheduleController::class, 'updateStatus'])->name('admin.schedules.status');
    
    // API Fetch Guru berdasarkan Mapel (Panggil dari Controller yang Benar!)
    // Cukup panggil '/get-teachers' karena sudah di dalam prefix 'admin'
    Route::get('/get-teachers/{subject_id}', [ScheduleController::class, 'getTeachers'])->name('admin.get-teachers');
    // Modul Input Soal Admin (Nested Resource)
Route::resource('schedules.questions', \App\Http\Controllers\Admin\QuestionController::class)
    ->names('admin.questions')
    ->parameters(['schedules' => 'schedule_id']); // Memastikan parameter di controller terbaca $schedule_id
    Route::post('/schedules/{schedule_id}/questions/copy', [App\Http\Controllers\Admin\QuestionController::class, 'copy'])->name('admin.questions.copy');
});

// --- GROUP GURU ---
Route::prefix('guru')->middleware(['auth', 'role:guru'])->name('guru.')->group(function () {
    Route::get('/dashboard', [GuruDash::class, 'index'])->name('dashboard');
    
    // Modul Input Soal (Ujian Terpusat)
    Route::resource('questions', UjianTerpusatController::class)->names('ujian-terpusat');
    Route::get('/questions/manage/{schedule_id}', [UjianTerpusatController::class, 'manage'])->name('ujian-terpusat.manage');
    Route::post('/ujian-terpusat/{schedule_id}/copy', [UjianTerpusatController::class, 'copy'])->name('ujian-terpusat.copy');

    // Modul Monitoring (Guru sebagai Pengawas)
    Route::get('/monitoring', [GuruMonitor::class, 'index'])->name('monitoring.index');
    Route::get('/monitoring/{id}', [GuruMonitor::class, 'show'])->name('monitoring.show');
});

// --- GROUP PENGAWAS MURNI ---
Route::prefix('pengawas')->middleware(['auth', 'role:pengawas'])->name('pengawas.')->group(function () {
    Route::get('/dashboard', [PengawasDash::class, 'index'])->name('dashboard');
    
    // Modul Monitoring (Pengawas Murni)
    Route::get('/monitoring', [PengawasMonitor::class, 'index'])->name('monitoring.index');
    Route::get('/monitoring/{id}', [PengawasMonitor::class, 'show'])->name('monitoring.show');
});