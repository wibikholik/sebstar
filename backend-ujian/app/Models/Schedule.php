<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
    'subject_id', 'classroom_id', 'teacher_ids','proctor_id',// Perhatikan 's' di belakang
    'tanggal_ujian', 'jam_mulai', 'jam_selesai', 'durasi', 'token', 'status', 'exam_type_id' // Tambahkan exam_type_id ke fillable
];

protected $casts = [
    'teacher_ids' => 'array', // Otomatis convert JSON ke Array
];

// Helper untuk mengambil data guru-guru tersebut
public function teachers()
{
    // Kita ambil user berdasarkan ID yang ada di dalam array teacher_ids
    return \App\Models\User::whereIn('id', $this->teacher_ids ?? [])->get();
}
public function subject() {
    return $this->belongsTo(Subject::class);
}
public function studentAnswers()
{
    return $this->hasMany(StudentAnswer::class, 'schedule_id');
}

public function classroom() {
    return $this->belongsTo(Classroom::class);
}
public function proctor()
{
    return $this->belongsTo(User::class, 'proctor_id');
}
public function examType()
{
    return $this->belongsTo(ExamType::class, 'exam_type_id');
}
}
