<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use App\Models\User;
// use App\Models\Question; // Import model lain di sini jika diperlukan
use App\Models\Schedule;
use App\Models\Classroom; 


class DashboardController extends Controller
{
    public function index()
    {
        // Mengambil jumlah berdasarkan role
        $jml_siswa = User::where('role', 'siswa')->count();
        $jml_guru = User::where('role', 'guru')->count();
        $jml_pengawas = User::where('role', 'pengawas')->count();
        $jml_schedule = Schedule::count();
        $jml_mata_pelajaran = Subject::count();
        $jml_kelas = Classroom::count();
        $jml_ujian_hari_ini = Schedule::whereDate('tanggal_ujian', now())->count();
      

        // Menyusun data untuk dikirim ke view
        // Pastikan nama key ('jml_siswa' dll) sama dengan yang dipanggil di Blade {{ $jml_siswa }}
        return view('admin.dashboard', [
            'jml_siswa'    => $jml_siswa,
            'jml_guru'     => $jml_guru,
            'jml_pengawas' => $jml_pengawas,
            'jml_schedule' => $jml_schedule,
            'jml_mata_pelajaran' => $jml_mata_pelajaran,
            'jml_kelas' => $jml_kelas,
            'jml_ujian_hari_ini' => $jml_ujian_hari_ini,
        ]);
    }
}