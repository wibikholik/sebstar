<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exam_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('schedules')->onDelete('cascade'); // Menghubungkan ke jadwal ujian
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Siswa yang melanggar
            $table->string('type'); // Jenis pelanggaran: 'keluar_aplikasi', 'screenshot', dll.
            $table->text('details')->nullable(); // Keterangan tambahan (misal: "Siswa membuka background app")
            $table->timestamp('created_at')->useCurrent(); // Waktu realtime pelanggaran
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_logs');
    }
};