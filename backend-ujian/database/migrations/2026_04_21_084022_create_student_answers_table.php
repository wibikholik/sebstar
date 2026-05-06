<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('student_answers', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained(); // Siswa yang menjawab
        $table->foreignId('schedule_id')->constrained(); // Ujian apa
        $table->foreignId('question_id')->constrained(); // Soal nomor berapa
        $table->string('answer'); // 'a', 'b', 'c', 'd', 'e'
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_answers');
    }
};
