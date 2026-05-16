<?php

namespace App\Http\Controllers\Pengawas;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Models\User;
use App\Models\ExamLog;
use Illuminate\Http\Request;
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

        // Ambil data siswa dengan counter jawaban dan pelanggaran secara live
        $students = User::where('role', 'siswa')
            ->where('classroom_id', $schedule->classroom_id)
            ->withCount([
                'studentAnswers as total_dijawab' => function ($query) use ($schedule) {
                    $query->where('schedule_id', $schedule->id);
                },
                'examLogs as total_pelanggaran' => function ($query) use ($schedule) {
                    $query->where('schedule_id', $schedule->id)
                          ->where('type', 'keluar_aplikasi');
                }
            ])
            ->orderBy('name', 'asc')
            ->get();

        // Riwayat pelanggaran real-time di ruangan ini
        $recentLogs = ExamLog::with('user')
            ->where('schedule_id', $id)
            ->latest()
            ->take(10)
            ->get();

        return view('pengawas.monitoring.show', compact('schedule', 'students', 'recentLogs'));
    }

    /**
     * Tombol Reset untuk hak akses Pengawas Ruangan
     */
    public function resetStudent($schedule_id, $student_id)
    {
        $student = User::where('id', $student_id)->where('role', 'siswa')->firstOrFail();
        
        $student->update([
            'is_logged_in' => 0
        ]);

        return redirect()->back()->with('success', "Siswa {$student->name} berhasil dilepas dari penguncian browser.");
    }
}