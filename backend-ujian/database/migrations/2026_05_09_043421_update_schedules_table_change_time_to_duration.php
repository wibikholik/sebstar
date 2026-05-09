<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
{
    Schema::table('schedules', function (Blueprint $table) {
        // Hapus jam yang lama jika masih ada
        if (Schema::hasColumn('schedules', 'jam_mulai')) {
            $table->dropColumn(['jam_mulai', 'jam_selesai']);
        }
        
        // Hanya buat kolom durasi jika memang BELUM ada
        if (!Schema::hasColumn('schedules', 'durasi')) {
            $table->integer('durasi')->default(60)->after('tanggal_ujian');
        }
    });
}

    /**
     * Mengembalikan perubahan (Rollback).
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn('durasi');
            $table->time('jam_mulai')->nullable();
            $table->time('jam_selesai')->nullable();
        });
    }
};