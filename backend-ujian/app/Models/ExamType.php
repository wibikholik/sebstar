<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExamType extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // Relasi ke Jadwal
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}