<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            // Laravel otomatis mengubahnya menjadi TEXT + Check Constraint di SQLite
            $table->enum('status', ['aktif', 'nonaktif', 'selesai'])
                  ->default('nonaktif')
                  ->change();
        });
    }

    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->enum('status', ['aktif', 'nonaktif'])
                  ->default('nonaktif')
                  ->change();
        });
    }
};
