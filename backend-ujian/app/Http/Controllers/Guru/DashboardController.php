<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Ambil ID user guru yang sedang login aktif
        $guruId = auth()->user()->id;

        // 2. HITUNG STATISTIK UTAMA BOX ATAS (Mendukung format JSON Angka ["2"] atau Integer [2])
        
        // PERBAIKAN UTAMA: Wajib memfilter status 'aktif' agar murni menampilkan ujian yang sedang berjalan sekarang!
        $jml_tugas_aktif = Schedule::where('status', 'aktif')
            ->where(function($query) use ($guruId) {
                $query->whereJsonContains('teacher_ids', (int)$guruId)
                      ->orWhereJsonContains('teacher_ids', (string)$guruId);
            })->count();
        
        // Menghitung jadwal pengerjaan khusus untuk hari ini saja
        $jml_jadwal_mengajar = Schedule::whereDate('tanggal_ujian', Carbon::today())
            ->where(function($query) use ($guruId) {
                $query->whereJsonContains('teacher_ids', (int)$guruId)
                      ->orWhereJsonContains('teacher_ids', (string)$guruId);
            })->count();

        // Menghitung jumlah rombel kelas unik yang diampu oleh guru ini
        $jml_kelas_diampu = Schedule::where(function($query) use ($guruId) {
            $query->whereJsonContains('teacher_ids', (int)$guruId)
                  ->orWhereJsonContains('teacher_ids', (string)$guruId);
        })->distinct('classroom_id')->count('classroom_id');

        // Mengambil susunan array ID kelas untuk menghitung total anak didik
        $kelasIds = Schedule::where(function($query) use ($guruId) {
            $query->whereJsonContains('teacher_ids', (int)$guruId)
                  ->orWhereJsonContains('teacher_ids', (string)$guruId);
        })->distinct()->pluck('classroom_id')->toArray();
            
        $jml_siswa_diajar = User::where('role', 'siswa')
            ->whereIn('classroom_id', $kelasIds)
            ->count();

        // Menghitung jumlah mata pelajaran unik yang diajar oleh guru ini
        $jml_mapel_guru = Schedule::where(function($query) use ($guruId) {
            $query->whereJsonContains('teacher_ids', (int)$guruId)
                  ->orWhereJsonContains('teacher_ids', (string)$guruId);
        })->distinct('subject_id')->count('subject_id');
        
        // Total seluruh paket sesi ujian terjadwal milik guru ini
        $jml_materi = Schedule::where(function($query) use ($guruId) {
            $query->whereJsonContains('teacher_ids', (int)$guruId)
                  ->orWhereJsonContains('teacher_ids', (string)$guruId);
        })->count(); 


        // 3. MENGHITUNG TUGAS BELUM DIPERIKSA DARI TABEL `student_answers`
        // (Asumsi is_graded = 0 berarti guru belum melakukan pemeriksaan lembar essay siswa)
        $jml_tugas_belum_diperiksa = DB::table('student_answers')
            ->join('schedules', 'student_answers.schedule_id', '=', 'schedules.id')
            ->where(function($query) use ($guruId) {
                $query->whereJsonContains('schedules.teacher_ids', (int)$guruId)
                      ->orWhereJsonContains('schedules.teacher_ids', (string)$guruId);
            })
            ->where('student_answers.is_graded', 0)
            ->distinct('student_answers.user_id')
            ->count('student_answers.user_id');


        // 4. GRAFIK 1: RATA-RATA NILAI PER KELAS YANG DIAJAR
        $subQueryTotalNilai = DB::table('student_answers')
            ->select('user_id', 'schedule_id', DB::raw('SUM(score) as total_skor_anak'))
            ->groupBy('user_id', 'schedule_id');

        $rataRataKelas = DB::table('schedules')
            ->join('classrooms', 'schedules.classroom_id', '=', 'classrooms.id')
            ->joinSub($subQueryTotalNilai, 'rekap_nilai', function ($join) {
                $join->on('schedules.id', '=', 'rekap_nilai.schedule_id');
            })
            ->where(function($query) use ($guruId) {
                $query->whereJsonContains('schedules.teacher_ids', (int)$guruId)
                      ->orWhereJsonContains('schedules.teacher_ids', (string)$guruId);
            })
            ->select('classrooms.nama_kelas', DB::raw('ROUND(AVG(rekap_nilai.total_skor_anak), 1) as rata_rata'))
            ->groupBy('classrooms.nama_kelas')
            ->orderBy('classrooms.nama_kelas', 'asc')
            ->get();

        $labelKelas = $rataRataKelas->pluck('nama_kelas')->toArray();
        $dataNilaiRataRata = $rataRataKelas->pluck('rata_rata')->toArray();

        if (empty($labelKelas)) {
            $labelKelas = ['Belum Ada Data Ujian'];
            $dataNilaiRataRata = [0];
        }


        // 5. PERBAIKAN GRAFIK 2: TREN SISWA SELESAI UJIAN (MENGGUNAKAN CARBON UNTUK SENIN - JUMAT MINGGU INI)
        // Kueri strtotime lama rawan bug jika pergantian tahun, kita ganti murni pakai Carbon agar kokoh
        $trenUjianSelesai = [];
        $startOfWeek = Carbon::now()->startOfWeek(); // Menunjuk ke hari Senin minggu ini

        for ($i = 0; $i < 5; $i++) {
            // Menghitung tanggal harian secara berantai: Senin (+0), Selasa (+1), Rabu (+2), dst...
            $dateString = $startOfWeek->copy()->addDays($i)->format('Y-m-d');
            
            $trenUjianSelesai[] = DB::table('student_answers')
                ->join('schedules', 'student_answers.schedule_id', '=', 'schedules.id')
                ->where(function($query) use ($guruId) {
                    $query->whereJsonContains('schedules.teacher_ids', (int)$guruId)
                          ->orWhereJsonContains('schedules.teacher_ids', (string)$guruId);
                })
                ->where('student_answers.is_finished', 1)
                ->whereDate('student_answers.updated_at', $dateString)
                ->distinct('student_answers.user_id')
                ->count('student_answers.user_id');
        }


        // 6. SELESAI & KIRIM KE BLADE
        return view('guru.dashboard', [
            'jml_siswa_diajar'          => $jml_siswa_diajar,
            'jml_kelas_diampu'           => $jml_kelas_diampu,
            'jml_tugas_aktif'            => $jml_tugas_aktif,
            'jml_jadwal_mengajar'        => $jml_jadwal_mengajar,
            'jml_mapel_guru'             => $jml_mapel_guru,
            'jml_materi'                 => $jml_materi,
            'jml_tugas_belum_diperiksa'  => $jml_tugas_belum_diperiksa,
            
            'labelKelas'                 => $labelKelas,
            'dataNilaiRataRata'          => $dataNilaiRataRata,
            'trenUjianSelesai'           => $trenUjianSelesai,
        ]);
    }
}