<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Question; 
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function getQuestions($id)
    {
        // Gunakan subject_id karena itulah kolom yang tersedia di Model Question
        // $id di sini adalah subject_id yang dikirim dari React Native
        $questions = Question::where('subject_id', $id)->get();

        return response()->json([
            'status' => 'success',
            'data' => $questions
        ]);
    }
}