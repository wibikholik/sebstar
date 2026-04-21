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
    Schema::table('schedules', function (Blueprint $table) {
        // Kita hubungkan ke tabel users, dan jika guru dihapus, set kolom ini jadi null
        $table->foreignId('teacher_id')->nullable()->after('classroom_id')->constrained('users')->onDelete('set null');
    });
}

public function down()
{
    Schema::table('schedules', function (Blueprint $table) {
        $table->dropForeign(['teacher_id']);
        $table->dropColumn('teacher_id');
    });
}
};
