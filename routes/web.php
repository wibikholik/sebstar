<?php

use App\Http\Controllers\Guru\UjianTerpusatController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController as AdminDash;
use App\Http\Controllers\Guru\DashboardController as GuruDash;
use App\Http\Controllers\Pengawas\DashboardController as PengawasDash;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\ClassroomController;
use App\Http\Controllers\Admin\MajorController;
use App\Http\Controllers\Admin\ScheduleController;





Route::get('/', function () {
    return view('welcome');
});

/// Group untuk Admin
Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminDash::class, 'index'])->name('admin.dashboard');

    // Route Resource
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->names('admin.users');
    Route::resource('subjects', \App\Http\Controllers\Admin\SubjectController::class)->names('admin.subjects');
    Route::resource('classrooms', \App\Http\Controllers\Admin\ClassroomController::class)->names('admin.classrooms');
    Route::resource('majors', \App\Http\Controllers\Admin\MajorController::class)->names('admin.majors');
    
    // PERBAIKAN DI SINI: Hapus '/admin' dari string URL karena sudah ada di prefix
    Route::get('/get-teachers/{subject_id}', function($subject_id) {
        $teachers = \App\Models\User::where('role', 'guru')
                                   ->where('subject_id', $subject_id)
                                   ->get(['id', 'name']);
        return response()->json($teachers);
    })->name('admin.get-teachers');
    
    // Perbaikan untuk Schedules
    Route::resource('schedules', \App\Http\Controllers\Admin\ScheduleController::class)->names('admin.schedules');
    
    // Route Status
    Route::post('schedules/{id}/status', [\App\Http\Controllers\Admin\ScheduleController::class, 'updateStatus'])->name('admin.schedules.status');
});

    
  


// Group untuk Guru
Route::prefix('guru')->middleware(['auth', 'role:guru'])->group(function () {
    Route::get('/dashboard', [GuruDash::class, 'index'])->name('guru.dashboard');
    Route::resource('questions', \App\Http\Controllers\Guru\UjianTerpusatController::class)->names('guru.ujian-terpusat');
    Route::get('/guru/questions/manage/{schedule_id}', [UjianTerpusatController::class, 'manage'])->name('guru.ujian-terpusat.manage');
});

// Group untuk Pengawas
Route::prefix('pengawas')->middleware(['auth', 'role:pengawas'])->group(function () {
    Route::get('/dashboard', [PengawasDash::class, 'index'])->name('pengawas.dashboard');
});

// Pintu Masuk
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');