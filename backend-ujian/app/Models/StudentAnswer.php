<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentAnswer extends Model
{
    protected $fillable = ['user_id', 'schedule_id', 'question_id', 'answer','is_finished'];
}