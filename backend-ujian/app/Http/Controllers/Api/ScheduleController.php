<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule;
use App\Models\StudentAnswer;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function index() 
    {
        // 1. Ambil semua jadwal dengan relasi subject dan classroom
        $jadwal = Schedule::with(['subject', 'classroom'])->get();

        // 2. Gunakan map untuk melengkapi data per item
        $jadwal->map(function ($item) {
            // A. Ambil data guru
            $item->teachers_data = User::whereIn('id', $item->teacher_ids ?? [])->get();
            
            // B. Cek apakah user sudah mengerjakan ujian ini
            $item->is_finished = StudentAnswer::where('user_id', Auth::id())
                                              ->where('schedule_id', $item->id)
                                              ->where('is_finished', true)
                                              ->exists();
            return $item;
        });

        return response()->json($jadwal, 200);
    }
}