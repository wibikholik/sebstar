<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::table('student_answers', function (Blueprint $table) {
    // Menambahkan skor untuk jawaban essay
    $table->decimal('score', 8, 2)->nullable()->after('answer'); 
    
    // Menambahkan catatan dari guru jika diperlukan
    $table->text('teacher_note')->nullable()->after('score');
    
    // Status apakah sudah dikoreksi guru atau belum
    $table->boolean('is_graded')->default(false)->after('is_finished');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_answers', function (Blueprint $table) {
            //
        });
    }
};
