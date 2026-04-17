<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    protected $fillable = [
    'subject_id', 'classroom_id', 'teacher_ids', // Perhatikan 's' di belakang
    'tanggal_ujian', 'jam_mulai', 'jam_selesai', 'durasi', 'token', 'status'
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

public function classroom() {
    return $this->belongsTo(Classroom::class);
}
}
