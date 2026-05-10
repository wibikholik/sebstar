<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    // Sesuaikan dengan kolom di database kamu
    protected $fillable = ['nama_mapel', 'kode_mapel'];

    // Relasi: Satu Mapel bisa punya banyak Jadwal
    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'subject_id');
    }
    public function teacher()
{
    return $this->belongsTo(User::class, 'teacher_id');
}
}