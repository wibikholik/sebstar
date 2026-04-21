<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Schedule; // Pastikan model Schedule di-import

class ScheduleController extends Controller
{
    public function index(Request $request)
{
    $user = $request->user();
    // Menampilkan semua jadwal tanpa where('status', 'aktif')
    return response()->json(['data' => \App\Models\Schedule::where('classroom_id', $user->classroom_id)->get()]);
}
}