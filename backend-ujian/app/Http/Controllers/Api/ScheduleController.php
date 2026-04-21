<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Queue\Events\JobAttempted;
use Illuminate\Support\Facades\Log;
use App\Models\Schedule; // Pastikan model Schedule di-import

class ScheduleController extends Controller
{
   public function index() {
    // Gunakan 'with' untuk subject dan classroom
    $jadwal = Schedule::with(['subject', 'classroom'])->get();

    // Opsional: Jika ingin menggabungkan data guru agar langsung bisa dibaca di frontend
    $jadwal->map(function ($item) {
        $item->teachers_data = \App\Models\User::whereIn('id', $item->teacher_ids ?? [])->get();
        return $item;
    });

    return response()->json([
        'success' => true,
        'data' => $jadwal
    ]);
   }
}