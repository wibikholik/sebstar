<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Schedule;
use App\Models\Classroom; 
use Illuminate\Support\Facades\DB; // WAJIB DI-IMPORT untuk query grafik

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Mengambil jumlah hitungan statistik utama
        $jml_siswa = User::where('role', 'siswa')->count();
        $jml_guru = User::where('role', 'guru')->count();
        $jml_pengawas = User::where('role', 'pengawas')->count();
        $jml_schedule = Schedule::count();
        $jml_mata_pelajaran = Subject::count();
        $jml_kelas = Classroom::count();
        
        // Sesuaikan kolom tanggal di tabel schedules kamu (misal: 'tanggal_ujian' atau 'tanggal')
        $jml_ujian_hari_ini = Schedule::whereDate('tanggal_ujian', now())->count();
      
        // 2. AMBIL DATA REAL-TIME UNTUK GRAFIK BATANG (JUMLAH SISWA PER KELAS)
        $siswaPerKelas = User::select('classrooms.nama_kelas', DB::raw('count(*) as total'))
            ->join('classrooms', 'users.classroom_id', '=', 'classrooms.id') // Menggabungkan tabel users dan classrooms
            ->where('users.role', 'siswa')
            ->groupBy('classrooms.nama_kelas')
            ->get();

        $kelasLabels = $siswaPerKelas->pluck('nama_kelas')->toArray();
        $kelasData = $siswaPerKelas->pluck('total')->toArray();

        // 3. AMBIL DATA REAL-TIME UNTUK GRAFIK GARIS (TREN MONITORING UJIAN 5 HARI)
        $trenUjianData = [];
        for ($i = 1; $i <= 5; $i++) {
            // Mengambil tanggal Senin s/d Jumat di minggu berjalan ini
            $dateString = date('Y-m-d', strtotime(date('Y-\WW-').$i));
            
            // Hitung ada berapa jadwal ujian pada tanggal tersebut
            $trenUjianData[] = Schedule::whereDate('tanggal_ujian', $dateString)->count();
        }

        // 4. Menyusun dan mengirimkan seluruh data ke file Blade Dashboard
        return view('admin.dashboard', [
            'jml_siswa'          => $jml_siswa,
            'jml_guru'           => $jml_guru,
            'jml_pengawas'       => $jml_pengawas,
            'jml_schedule'       => $jml_schedule,
            'jml_mata_pelajaran' => $jml_mata_pelajaran,
            'jml_kelas'          => $jml_kelas,
            'jml_ujian_hari_ini' => $jml_ujian_hari_ini,
            
            // Variabel penentu grafik yang tadi bikin error:
            'kelasLabels'        => $kelasLabels,
            'kelasData'          => $kelasData,
            'trenUjianData'      => $trenUjianData,
        ]);
    }
}