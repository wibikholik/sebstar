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
use App\Http\Controllers\Admin\ExamTypeController;
use App\Http\Controllers\Admin\QuestionController;

// Guru Controllers
use App\Http\Controllers\Guru\DashboardController as GuruDash;
use App\Http\Controllers\Guru\UjianTerpusatController;
use App\Http\Controllers\Guru\MonitoringController as GuruMonitor;
use App\Http\Controllers\Guru\KoreksiController;

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
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminDash::class, 'index'])->name('admin.dashboard');

    // Manajemen Data Master
    Route::resource('users', UserController::class)->names('admin.users');
    Route::resource('subjects', SubjectController::class)->names('admin.subjects');
    Route::resource('classrooms', ClassroomController::class)->names('admin.classrooms');
    Route::resource('majors', MajorController::class)->names('admin.majors');
    
    // MODUL TYPE UJIAN
    Route::resource('exam-types', ExamTypeController::class)
        ->names('admin.exam-types')
        ->except(['show', 'create', 'edit']);
    
    // Penjadwalan & Token
    Route::resource('schedules', ScheduleController::class)->names('admin.schedules');
    Route::post('schedules/{id}/status', [ScheduleController::class, 'updateStatus'])->name('admin.schedules.status');
    
    // API Fetch Guru
    Route::get('/get-teachers/{subject_id}', [ScheduleController::class, 'getTeachers'])->name('admin.get-teachers');

    // Modul Input Soal Admin (Nested Resource)
    Route::resource('schedules.questions', QuestionController::class)
        ->names('admin.questions')
        ->parameters(['schedules' => 'schedule_id']); 

    Route::post('/schedules/{schedule_id}/questions/copy', [QuestionController::class, 'copy'])->name('admin.questions.copy');
});

// --- GROUP GURU ---
Route::prefix('guru')->middleware(['auth', 'role:guru'])->name('guru.')->group(function () {
    
    // 1. Dashboard
    Route::get('/dashboard', [GuruDash::class, 'index'])->name('dashboard');
    
    // 2. Modul Jadwal Ujian (Mandiri & Pusat)
    Route::get('/schedules', [App\Http\Controllers\Guru\ScheduleController::class, 'index'])->name('schedules.index');
    Route::post('/schedules', [App\Http\Controllers\Guru\ScheduleController::class, 'store'])->name('schedules.store');
    Route::put('/schedules/{id}', [App\Http\Controllers\Guru\ScheduleController::class, 'update'])->name('schedules.update');
    Route::delete('/schedules/{id}', [App\Http\Controllers\Guru\ScheduleController::class, 'destroy'])->name('schedules.destroy');

    // 3. Modul Manajemen Soal (Questions)
    Route::resource('questions', \App\Http\Controllers\Guru\QuestionController::class)->names('questions');
    Route::get('/questions/manage/{schedule_id}', [\App\Http\Controllers\Guru\QuestionController::class, 'manage'])->name('questions.manage');
    Route::post('/questions/copy/{schedule_id}', [\App\Http\Controllers\Guru\QuestionController::class, 'copy'])->name('questions.copy');

    // 4. Modul Monitoring (DITAMBAHKAN ROUTE RESET SISWA)
    Route::get('/monitoring', [GuruMonitor::class, 'index'])->name('monitoring.index');
    Route::get('/monitoring/{id}', [GuruMonitor::class, 'show'])->name('monitoring.show');
    Route::post('/monitoring/{schedule_id}/reset/{student_id}', [GuruMonitor::class, 'resetStudent'])->name('monitoring.reset');

    // 5. Modul Koreksi Nilai
    Route::get('/koreksi', [KoreksiController::class, 'listSchedules'])->name('koreksi.list');
    Route::get('/koreksi/jadwal/{schedule_id}', [KoreksiController::class, 'index'])->name('koreksi.index');
    Route::post('/koreksi/weight/{schedule_id}', [KoreksiController::class, 'storeWeight'])->name('koreksi.storeWeight');
    Route::get('/koreksi/export/{schedule_id}', [KoreksiController::class, 'exportExcel'])->name('koreksi.export');

    Route::resource('koreksi', KoreksiController::class)->only(['show', 'update'])->parameters([
        'koreksi' => 'user_id'
    ]);
});

// --- GROUP PENGAWAS MURNI ---
Route::prefix('pengawas')->middleware(['auth', 'role:pengawas'])->name('pengawas.')->group(function () {
    Route::get('/dashboard', [PengawasDash::class, 'index'])->name('dashboard');
    
    // Modul Monitoring (DITAMBAHKAN ROUTE RESET SISWA)
    Route::get('/monitoring', [PengawasMonitor::class, 'index'])->name('monitoring.index');
    Route::get('/monitoring/{id}', [PengawasMonitor::class, 'show'])->name('monitoring.show');
    Route::post('/monitoring/{schedule_id}/reset/{student_id}', [PengawasMonitor::class, 'resetStudent'])->name('monitoring.reset');
});