<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'nis',        // Untuk Siswa
        'nip',        // Untuk Guru
        'subject_id',  // Relasi ke Mapel (Guru)
        'classroom_id', // Relasi ke Kelas (Siswa) - Tambahkan ini
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relasi ke Mata Pelajaran (Khusus Guru)
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    /**
     * Relasi ke Kelas (Khusus Siswa)
     */
    public function classroom()
    {
        return $this->belongsTo(Classroom::class, 'classroom_id');
    }
}