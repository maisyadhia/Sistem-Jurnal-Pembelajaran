<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('kelas_master', function (Blueprint $table) {
            $table->string('tahun_ajaran', 9)->default('2026/2027')->after('wali_kelas');
            $table->boolean('is_aktif')->default(true)->after('tahun_ajaran');
            
            $table->index('tahun_ajaran');
            $table->index('is_aktif');
        });
    }

    public function down()
    {
        Schema::table('kelas_master', function (Blueprint $table) {
            $table->dropIndex(['tahun_ajaran']);
            $table->dropIndex(['is_aktif']);
            $table->dropColumn(['tahun_ajaran', 'is_aktif']);
        });
    }
};