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
    Schema::table('classrooms', function (Blueprint $table) {
        // Hapus kolom jika sudah terlanjur ada akibat error sebelumnya
        if (Schema::hasColumn('classrooms', 'major_id')) {
            $table->dropColumn('major_id');
        }
    });

    // Baru buat ulang dengan benar
    Schema::table('classrooms', function (Blueprint $table) {
        if (Schema::hasColumn('classrooms', 'jurusan')) {
            $table->dropColumn('jurusan');
        }
        $table->foreignId('major_id')->nullable()->constrained()->onDelete('cascade');
    });
}

public function down()
{
    Schema::table('classrooms', function (Blueprint $table) {
        $table->dropForeign(['major_id']);
        $table->dropColumn('major_id');
        $table->string('jurusan')->nullable();
    });
}
};
