<?php

namespace App\Exports;

use App\Models\User;
use App\Models\StudentAnswer;
use App\Models\Schedule;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class NilaiUjianExport implements FromQuery, WithMapping, WithHeadings, WithStyles
{
    protected $schedule_id, $bobotPg, $bobotEssay, $schedule;

    public function __construct($schedule_id, $bobotPg, $bobotEssay)
    {
        $this->schedule_id = $schedule_id;
        $this->bobotPg = $bobotPg / 100;
        $this->bobotEssay = $bobotEssay / 100;
        $this->schedule = Schedule::with('subject')->find($schedule_id);
    }

    public function query()
    {
        return User::whereHas('answers', fn($q) => $q->where('schedule_id', $this->schedule_id));
    }

    public function headings(): array
    {
        return [
            ['LAPORAN HASIL UJIAN SEBSTAR'],
            ['Mata Pelajaran:', $this->schedule->subject->nama_mapel ?? 'N/A'],
            ['Tanggal Ujian:', $this->schedule->tanggal_ujian],
            ['Bobot Nilai:', "PG: " . ($this->bobotPg * 100) . "% | Essay: " . ($this->bobotEssay * 100) . "%"],
            [''], // Baris Kosong
            ['NAMA LENGKAP SISWA', 'SKOR PG (ASLI)', 'SKOR ESSAY (ASLI)', 'NILAI AKHIR (BERBOBOT)']
        ];
    }

    public function map($student): array
    {
        $answers = StudentAnswer::where('user_id', $student->id)
                                ->where('schedule_id', $this->schedule_id)
                                ->get();

        // Hitung PG (Skala 100)
        $totalPg = $answers->where('type', 'pg')->count() ?: 1;
        $benarPg = $answers->where('is_correct', 1)->count();
        $nilaiPgRaw = ($benarPg / $totalPg) * 100;

        // Hitung Essay (Skala 100)
        $nilaiEssayRaw = $answers->sum('score');

        // Kalkulasi Akhir
        $nilaiAkhir = ($nilaiPgRaw * $this->bobotPg) + ($nilaiEssayRaw * $this->bobotEssay);

        return [
            $student->name,
            round($nilaiPgRaw, 2),
            round($nilaiEssayRaw, 2),
            round($nilaiAkhir, 2)
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            6 => ['font' => ['bold' => true]], // Header Tabel
            1 => ['font' => ['bold' => true, 'size' => 14]], // Judul Laporan
        ];
    }
}