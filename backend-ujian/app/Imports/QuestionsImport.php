<?php

namespace App\Imports;

use App\Models\Question;
use App\Models\Schedule;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class QuestionsImport implements ToModel, WithHeadingRow
{
    protected $scheduleId;

    public function __construct($scheduleId)
    {
        $this->scheduleId = $scheduleId;
    }

    /**
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Abaikan baris jika tipe atau teks soal kosong
        if (empty($row['type']) || empty($row['question_text'])) {
            return null;
        }

        $type = strtolower(trim($row['type']));

        // Ambil data jadwal ujian untuk mendapatkan subject_id (ID Mapel) otomatis
        $schedule = Schedule::find($this->scheduleId);
        $subjectId = $schedule ? $schedule->subject_id : null;

        return new Question([
            'subject_id'     => $subjectId,              // Otomatis dari jadwal ujian
            'user_id'        => auth()->id(),            // ID Guru yang sedang login
            'schedule_id'    => $this->scheduleId,       // Mengunci ke jadwal aktif
            'type'           => $type,                   // pg / essay
            'question_text'  => $row['question_text'],
            'question_image' => null,                    // Default null saat import massal teks
            
            // Opsi pilihan ganda A sampai E (Kondisional sesuai tipe)
            'option_a'       => $type === 'pg' ? ($row['option_a'] ?? null) : null,
            'option_b'       => $type === 'pg' ? ($row['option_b'] ?? null) : null,
            'option_c'       => $type === 'pg' ? ($row['option_c'] ?? null) : null,
            'option_d'       => $type === 'pg' ? ($row['option_d'] ?? null) : null,
            'option_e'       => $type === 'pg' ? ($row['option_e'] ?? null) : null,
            
            // Kunci jawaban (A/B/C/D/E murni huruf kapital)
            'correct_answer' => $type === 'pg' ? strtoupper(trim($row['correct_answer'])) : null,
        ]);
    }
}