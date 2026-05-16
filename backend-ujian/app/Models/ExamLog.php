<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamLog extends Model
{
    // Karena kita hanya butuh waktu buat tanpa di-update, nonaktifkanupdated_at otomatis jika perlu, 
    // tapi cara paling aman set timestamps ke false jika hanya memakai created_at manual:
    public $timestamps = false; 

    protected $fillable = [
        'schedule_id',
        'user_id',
        'type',
        'details',
        'created_at'
    ];

    // Relasi balik ke Siswa
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}