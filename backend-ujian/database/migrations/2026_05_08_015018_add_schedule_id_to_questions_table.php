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
        Schema::table('questions', function (Blueprint $blueprint) {
            // 1. Tambahkan kolom schedule_id setelah subject_id
            // constrained('schedules') artinya dia otomatis merujuk ke tabel schedules
            // cascadeOnDelete artinya jika jadwal dihapus, soal di dalamnya ikut terhapus
            $blueprint->foreignId('schedule_id')
                      ->after('subject_id')
                      ->nullable() // Beri nullable dulu agar data lama tidak error saat migrate
                      ->constrained('schedules')
                      ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $blueprint) {
            // Hapus foreign key dan kolomnya jika migrasi di-rollback
            $blueprint->dropForeign(['schedule_id']);
            $blueprint->dropColumn('schedule_id');
        });
    }
};