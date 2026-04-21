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
    Schema::create('questions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('subject_id')->constrained()->onDelete('cascade');
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->enum('type', ['pg', 'essay']); // Pembeda tipe soal
        $table->text('question_text');
        $table->string('question_image')->nullable();
        
        // Kolom Pilihan Ganda (Boleh Null jika tipe Essay)
        $table->text('option_a')->nullable();
        $table->text('option_b')->nullable();
        $table->text('option_c')->nullable();
        $table->text('option_d')->nullable();
        $table->text('option_e')->nullable();
        
        // Jawaban
        // Jika PG: isi A/B/C/D/E. Jika Essay: isi kunci jawaban singkat/pedoman nilai
        $table->text('correct_answer')->nullable(); 
        
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
};
