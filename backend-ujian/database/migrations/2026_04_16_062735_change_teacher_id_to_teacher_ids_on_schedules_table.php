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
        // Hapus foreign key lama jika ada
        if (Schema::hasColumn('schedules', 'teacher_id')) {
            $table->dropForeign(['teacher_id']);
            $table->dropColumn('teacher_id');
        }
        // Tambahkan kolom baru untuk menampung banyak ID guru
        $table->json('teacher_ids')->nullable()->after('classroom_id');
    });
}
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('teacher_ids_on_schedules', function (Blueprint $table) {
            //
        });
    }
};
