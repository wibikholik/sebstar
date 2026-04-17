<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi untuk menambah kolom.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Kolom untuk Siswa
            $table->string('nis')->nullable()->unique()->after('role');
            $table->string('kelas')->nullable()->after('nis');

            // Kolom untuk Guru
            $table->string('nip')->nullable()->unique()->after('kelas');
            $table->string('mapel')->nullable()->after('nip');
        });
    }

    /**
     * Batalkan migrasi (Rollback).
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['nis', 'kelas', 'nip', 'mapel']);
        });
    }
};