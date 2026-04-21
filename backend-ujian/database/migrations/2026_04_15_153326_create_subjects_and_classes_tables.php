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
    // Tabel Mata Pelajaran
    Schema::create('subjects', function (Blueprint $table) {
        $table->id();
        $table->string('nama_mapel');
        $table->string('kode_mapel')->unique(); // Contoh: BIN-01
        $table->timestamps();
    });

    // Tabel Kelas
    Schema::create('classrooms', function (Blueprint $table) {
        $table->id();
        $table->string('nama_kelas'); // Contoh: XII RPL 1
        $table->string('jurusan');    // Contoh: Rekayasa Perangkat Lunak
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects_and_classes_tables');
    }
};
