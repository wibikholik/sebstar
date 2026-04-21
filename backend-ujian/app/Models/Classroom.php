<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Classroom extends Model
{
    protected $fillable = ['nama_kelas', 'major_id'];
    public function major()
    {
        return $this->belongsTo(Major::class, 'major_id');
    }
}

