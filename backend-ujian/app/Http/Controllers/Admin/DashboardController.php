<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
// use App\Models\Question; // Import model lain di sini jika diperlukan

class DashboardController extends Controller
{
    public function index()
    {
        // Mengambil jumlah berdasarkan role
        $jml_siswa = User::where('role', 'siswa')->count();
        $jml_guru = User::where('role', 'guru')->count();
        $jml_pengawas = User::where('role', 'pengawas')->count();

        // Menyusun data untuk dikirim ke view
        // Pastikan nama key ('jml_siswa' dll) sama dengan yang dipanggil di Blade {{ $jml_siswa }}
        return view('admin.dashboard', [
            'jml_siswa'    => $jml_siswa,
            'jml_guru'     => $jml_guru,
            'jml_pengawas' => $jml_pengawas,
        ]);
    }
}