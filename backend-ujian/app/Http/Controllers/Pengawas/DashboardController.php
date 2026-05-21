<?php

namespace App\Http\Controllers\Pengawas;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Schedule; // Pastikan model Schedule di-import
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard utama pengawas.
     */
    public function index()
    {
        // 1. Ambil ID Pengawas yang sedang login
        $pengawasId = Auth::id();

        // 2. Ambil jadwal ujian di mana staf ini ditugaskan sebagai proctor_id
        $schedules = Schedule::with(['subject', 'classroom'])
            ->where('proctor_id', $pengawasId) // Menggunakan proctor_id sesuai database kamu
            ->latest()
            ->get();

        // 3. Return ke halaman view dashboard pengawas
        return view('pengawas.dashboard', compact('schedules'));
    }
}