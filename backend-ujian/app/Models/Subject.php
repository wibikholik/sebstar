<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['nama_mapel', 'kode_mapel'];
    public function subject()
{
    return $this->belongsTo(Subject::class, 'subject_id');
}

public function classroom()
{
    return $this->belongsTo(Classroom::class, 'classroom_id');
}
}

