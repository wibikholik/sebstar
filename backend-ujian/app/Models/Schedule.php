<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_type_id', 'subject_id', 'classroom_id', 
        'teacher_ids', 'proctor_id', 'tanggal_ujian', 
        'durasi', 'token', 'status', 'created_by',
        'weight_pg', 'weight_essay'
    ];

    protected $casts = [
        'teacher_ids' => 'array',
        'tanggal_ujian' => 'date', 
    ];

    /**
     * RELASI UTAMA KE SOAL
     * Ini yang bikin error "Call to undefined method" tadi
     */
    public function questions()
    {
        return $this->hasMany(Question::class, 'schedule_id');
    }

    // Relasi ke Pembuat Jadwal
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function examType()
    {
        return $this->belongsTo(ExamType::class, 'exam_type_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'classroom_id');
    }

    public function proctor()
    {
        return $this->belongsTo(User::class, 'proctor_id');
    }

    public function studentAnswers()
    {
        return $this->hasMany(StudentAnswer::class, 'schedule_id');
    }

    /**
     * Helper Guru (JSON)
     */
    public function getTeachersAttribute()
    {
        return User::whereIn('id', $this->teacher_ids ?? [])->get();
    }
}