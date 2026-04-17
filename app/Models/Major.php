<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Major extends Model
{
    use HasFactory;

    // Tambahkan baris ini untuk "membuka kunci" kolom di database
    protected $fillable = [
        'nama_jurusan',
        'singkatan',
    ];
    public function classrooms()
    {
        return $this->hasMany(Classroom::class, 'major_id');
    }
}