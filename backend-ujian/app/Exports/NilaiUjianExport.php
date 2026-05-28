<?php

namespace App\Exports;

use App\Models\User;
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
        $this->bobotPg = $bobotPg;
        $this->bobotEssay = $bobotEssay;
        $this->schedule = Schedule::with('subject')->find($schedule_id);
    }

    /**
     * Ambil data siswa lengkap dengan hitungan matematika yang SAMA PERSIS dengan Controller
     */
    public function query()
    {
        return User::whereHas('answers', function($q) {
            $q->where('schedule_id', $this->schedule_id);
        })
        // Hitung total soal PG yang dijawab siswa
        ->withCount(['answers as total_pg' => function($q) {
            $q->where('schedule_id', $this->schedule_id)
              ->whereHas('question', fn($query) => $query->where('type', 'pg'));
        }])
        // Hitung jumlah jawaban PG siswa yang BENAR (Logika ini terbukti sukses di dashboard kamu)
        ->withCount(['answers as benar_pg' => function($q) {
            $q->where('schedule_id', $this->schedule_id)
              ->whereHas('question', function($query) {
                  $query->where('type', 'pg')
                        ->whereRaw('student_answers.answer = questions.correct_answer'); 
              });
        }])
        // Ambil rata-rata nilai essay (Skala otomatis dikonversi kembali ke 0 - 100)
        ->withAvg(['answers as avg_skor_essay' => function($q) {
            $q->where('schedule_id', $this->schedule_id)
              ->where('is_graded', true)
              ->whereHas('question', fn($query) => $query->where('type', 'essay'));
        }], 'score');
    }

    /**
     * Membuat Baris Informasi Header Dokumen Excel
     */
    public function headings(): array
    {
        return [
            ['LAPORAN HASIL UJIAN SEBSTAR'],
            ['Mata Pelajaran:', $this->schedule->subject->nama_mapel ?? 'Tidak Diketahui'],
            ['Tanggal Ujian:', $this->schedule->tanggal_ujian ? \Carbon\Carbon::parse($this->schedule->tanggal_ujian)->translatedFormat('d F Y') : '-'],
            ['Bobot Nilai:', "Pilihan Ganda: " . $this->bobotPg . "% | Essay: " . $this->bobotEssay . "%"],
            [''], // Baris Kosong Pemisah
            ['NAMA LENGKAP SISWA', 'SKOR PILIHAN GANDA (MURNI)', 'SKOR ESSAY (RATA-RATA)', 'NILAI AKHIR (BERBOBOT)']
        ];
    }

    /**
     * Memetakan data kalkulasi nilai masing-masing siswa (Mengambil langsung dari hasil query database di atas)
     */
    public function map($student): array
    {
        // 1. Kalkulasi Nilai PG Murni (0-100)
        $nilaiPgRaw = ($student->total_pg > 0) ? ($student->benar_pg / $student->total_pg) * 100 : 0;

        // 2. Ambil Nilai Essay Murni (Rata-rata)
        $nilaiEssayRaw = $student->avg_skor_essay ?? 0;

        // 3. Hitung Gabungan Nilai Akhir Berdasarkan Bobot Persen
        $nilaiAkhir = ($nilaiPgRaw * ($this->bobotPg / 100)) + ($nilaiEssayRaw * ($this->bobotEssay / 100));

        return [
            $student->name,
            round($nilaiPgRaw, 2),
            round($nilaiEssayRaw, 2),
            round($nilaiAkhir, 2)
        ];
    }

    /**
     * Styling Tampilan Tabel Spreadsheet
     */
    public function styles(Worksheet $sheet)
    {
        // Auto-fit kolom agar tidak ada teks nama terpotong
        foreach (range('A', 'D') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [
            1 => ['font' => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'CD0000']]], // Judul Laporan Merah Sebstar
            2 => ['font' => ['bold' => true]],
            3 => ['font' => ['bold' => true]],
            4 => ['font' => ['bold' => true]],
            6 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '1E293B'] // Background Header Abu-abu Gelap Premium
                ]
            ],
        ];
    }
}