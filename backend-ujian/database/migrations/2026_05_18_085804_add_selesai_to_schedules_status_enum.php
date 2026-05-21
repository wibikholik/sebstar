<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Menggunakan Native SQL karena merubah ENUM di Laravel paling aman lewat raw query
        DB::statement("ALTER TABLE schedules MODIFY COLUMN status ENUM('aktif', 'nonaktif', 'selesai') NOT NULL DEFAULT 'nonaktif'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE schedules MODIFY COLUMN status ENUM('aktif', 'nonaktif') NOT NULL DEFAULT 'nonaktif'");
    }
};