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
        // Gunakan nullable() jika sudah ada data jadwal sebelumnya agar tidak error saat di-migrate
        $table->foreignId('exam_type_id')->nullable()->after('id')->constrained('exam_types')->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('schedules', function (Blueprint $table) {
        $table->dropForeign(['exam_type_id']);
        $table->dropColumn('exam_type_id');
    });
}
};
