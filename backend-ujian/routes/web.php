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

    // 🚀 FITUR: Rute Khusus Import & Download Template Excel PENGGUNA SEBSTAR
    Route::post('/users/import', [UserController::class, 'importExcel'])->name('admin.users.import');
    Route::get('/users/download-template', [UserController::class, 'downloadTemplate'])->name('admin.users.download_template');

    // Manajemen Data Master Users
    Route::resource('users', UserController::class)->names('admin.users');
    
    // 🚀 FITUR BARU: Rute Khusus Import & Download Template Excel MATA PELAJARAN (SUBJECTS)
    // Ditambahkan di sini agar menghentikan error "not defined" di Blade
    Route::get('/subjects/download-template', [SubjectController::class, 'downloadTemplate'])->name('admin.subjects.template');
    Route::post('/subjects/import-excel', [SubjectController::class, 'importExcel'])->name('admin.subjects.import');

    // Manajemen Data Master Subjects
    Route::resource('subjects', SubjectController::class)->names('admin.subjects');
    
   // 🚀 ROUTE CUSTOM UNTUK IMPORT & DOWNLOAD TEMPLATE GABUNGAN
Route::get('classrooms/download-template', [ClassroomController::class, 'downloadTemplateGabungan'])->name('admin.classrooms.download_template');
Route::post('classrooms/import-gabungan', [ClassroomController::class, 'importGabungan'])->name('admin.classrooms.import');

// Route Resource Bawaan (Tetap di bawah)
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

    // 🚀 FITUR: Rute Khusus Import & Download Template Excel SOAL/QUESTIONS SEBSTAR
    Route::get('/questions/download-template', [QuestionController::class, 'downloadTemplate'])->name('admin.questions.download_template');
    Route::post('/schedules/{schedule_id}/questions/import', [QuestionController::class, 'importExcel'])->name('admin.questions.import');

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
    
    // 2. Modul Jadwal Ujian
    Route::get('/schedules', [App\Http\Controllers\Guru\ScheduleController::class, 'index'])->name('schedules.index');
    Route::post('/schedules', [App\Http\Controllers\Guru\ScheduleController::class, 'store'])->name('schedules.store');
    Route::put('/schedules/{id}', [App\Http\Controllers\Guru\ScheduleController::class, 'update'])->name('schedules.update');
    Route::delete('/schedules/{id}', [App\Http\Controllers\Guru\ScheduleController::class, 'destroy'])->name('schedules.destroy');
    Route::post('/schedules/{id}/toggle-status', [App\Http\Controllers\Guru\ScheduleController::class, 'toggleStatus'])->name('schedules.toggle-status');

    // 3. Modul Manajemen Soal (Questions)
    Route::get('/questions/download-template', [\App\Http\Controllers\Guru\QuestionController::class, 'downloadTemplate'])->name('questions.download_template');
    Route::post('/questions/import/{schedule_id}', [\App\Http\Controllers\Guru\QuestionController::class, 'importExcel'])->name('questions.import');
    Route::post('/questions/copy/{schedule_id}', [\App\Http\Controllers\Guru\QuestionController::class, 'copy'])->name('questions.copy');
    Route::get('/questions/manage/{schedule_id}', [\App\Http\Controllers\Guru\QuestionController::class, 'manage'])->name('questions.manage');

    Route::resource('questions', \App\Http\Controllers\Guru\QuestionController::class)
        ->names('questions')
        ->except(['show', 'create', 'edit']);

    // 4. Modul Monitoring
    Route::get('/monitoring', [GuruMonitor::class, 'index'])->name('monitoring.index');
    Route::get('/monitoring/{id}', [GuruMonitor::class, 'show'])->name('monitoring.show');
    Route::post('/monitoring/{schedule_id}/reset/{student_id}', [GuruMonitor::class, 'resetStudent'])->name('monitoring.reset');
    Route::patch('/monitoring/{schedule}/update-status', [GuruMonitor::class, 'updateStatus'])->name('monitoring.updateStatus');
    Route::post('/monitoring/{schedule}/student/{student}/force-submit', [GuruMonitor::class, 'forceSubmit'])->name('monitoring.forceSubmit');

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
    
    // Modul Monitoring
    Route::get('/monitoring', [PengawasMonitor::class, 'index'])->name('monitoring.index');
    Route::get('/monitoring/{id}', [PengawasMonitor::class, 'show'])->name('monitoring.show');
    Route::post('/monitoring/{schedule_id}/reset/{student_id}', [PengawasMonitor::class, 'resetStudent'])->name('monitoring.reset');
});