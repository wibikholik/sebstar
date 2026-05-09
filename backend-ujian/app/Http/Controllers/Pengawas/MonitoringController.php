<?php

namespace App\Http\Controllers\Pengawas;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class MonitoringController extends Controller
{
    public function index()
    {
        $schedules = Schedule::with(['subject', 'classroom'])
            ->where('proctor_id', Auth::id())
            ->latest()
            ->get();

        return view('pengawas.monitoring.index', compact('schedules'));
    }

    public function show($id)
    {
        $schedule = Schedule::with(['subject', 'classroom'])->findOrFail($id);

        // Security check khusus Pengawas
        if ($schedule->proctor_id !== Auth::id()) {
            abort(403, 'Akses Ditolak. Anda tidak ditugaskan di ruang ini.');
        }

        // Ambil data siswa yang harus ikut ujian di kelas ini
        $students = User::where('role', 'siswa')
            ->where('classroom_id', $schedule->classroom_id)
            ->orderBy('name', 'asc')
            ->get();

        return view('pengawas.monitoring.show', compact('schedule', 'students'));
    }
}