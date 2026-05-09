<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\StudentAnswer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{public function index() 
{
    // Ambil user beserta relasi kelasnya agar data kelas tampil di Header Mobile
    $user = User::with('classroom')->find(Auth::id());

    // 1. Ambil jadwal KHUSUS untuk kelas siswa tersebut
    $jadwal = Schedule::with(['subject', 'classroom', 'examType'])
        ->where('classroom_id', $user->classroom_id)
        ->get();

    $jadwal->map(function ($item) {
        $item->teachers_data = User::whereIn('id', $item->teacher_ids ?? [])
            ->select('id', 'name')
            ->get();
        
        $item->is_finished = StudentAnswer::where('user_id', Auth::id())
            ->where('schedule_id', $item->id)
            ->where('is_finished', true)
            ->exists();

        return $item;
    });

    return response()->json([
        'status' => 'success',
        'data' => $jadwal,
        'user' => $user // Kirim data user lengkap ke mobile
    ], 200);
}
}