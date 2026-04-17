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
    Schema::create('schedules', function (Blueprint $table) {
        $table->id();
        $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
        $table->foreignId('classroom_id')->constrained('classrooms')->onDelete('cascade');
        $table->date('tanggal_ujian');
        $table->time('jam_mulai');
        $table->time('jam_selesai');
        $table->integer('durasi'); // dalam menit
        $table->string('token', 6)->unique(); // Token untuk masuk ujian
        $table->enum('status', ['aktif', 'nonaktif'])->default('nonaktif');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
