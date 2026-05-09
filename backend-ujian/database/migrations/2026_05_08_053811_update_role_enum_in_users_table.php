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
    // Gunakan DB statement karena perubahan ENUM terkadang sulit lewat Blueprint biasa
    DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'guru', 'siswa', 'pengawas') NOT NULL");
}

public function down()
{
    DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'guru', 'siswa') NOT NULL");
}
};
