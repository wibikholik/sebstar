<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_id',
        'user_id',
        'schedule_id', // Tambahkan schedule_id ke fillable agar bisa diisi saat create/update
        'type',
        'question_text',
        'question_image',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'option_e',
        'correct_answer',
    ];

    /**
     * Relasi ke Mata Pelajaran
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Relasi ke Guru (User)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    /**
     * Relasi ke Jadwal Ujian
     */
    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
    public function studentAnswers()
    {
        // Pastikan foreign key di tabel student_answers bernama 'question_id'
        return $this->hasMany(StudentAnswer::class, 'question_id');
    }
}