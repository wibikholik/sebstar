<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAnswer extends Model
{
   protected $fillable = [
    'user_id',
    'schedule_id',
    'question_id',
    'answer',
    'score',        
    'teacher_note', 
    'is_finished',
    'is_graded'     
];
public function question()
{
    // Hubungkan ke model Question menggunakan foreign key question_id
    return $this->belongsTo(Question::class, 'question_id');
}
}