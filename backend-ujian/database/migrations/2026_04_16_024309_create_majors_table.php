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
    Schema::create('majors', function (Blueprint $table) {
        $table->id();
        $table->string('nama_jurusan')->unique(); // Contoh: Rekayasa Perangkat Lunak
        $table->string('singkatan'); // Contoh: RPL
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('majors');
    }
};
