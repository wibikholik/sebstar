<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $jml_siswa = \App\Models\User::where('role', 'siswa')->count();
        $jml_guru = \App\Models\User::where('role', 'guru')->count();
        $jml_pengawas = \App\Models\User::where('role', 'pengawas')->count();
        $data = [
            'jml_siswa'    => $jml_siswa,
            'jml_guru'     => $jml_guru,
            'jml_pengawas' => $jml_pengawas,
            // 'jml_soal'  => Question::count(), // Aktifkan jika model Soal sudah ada
        ];
        return view('admin.dashboard', $data);
    }
}
