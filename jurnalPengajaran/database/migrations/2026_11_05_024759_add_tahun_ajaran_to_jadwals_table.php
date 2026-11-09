<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('jadwals', function (Blueprint $table) {
            if (!Schema::hasColumn('jadwals', 'tahun_ajaran')) {
                $table->string('tahun_ajaran', 9)->default('2026/2027')->after('jam_selesai');
            }
            if (!Schema::hasColumn('jadwals', 'is_aktif')) {
                $table->boolean('is_aktif')->default(true)->after('tahun_ajaran');
            }
        });
    }

    public function down()
    {
        Schema::table('jadwals', function (Blueprint $table) {
            if (Schema::hasColumn('jadwals', 'tahun_ajaran')) {
                $table->dropColumn('tahun_ajaran');
            }
            if (Schema::hasColumn('jadwals', 'is_aktif')) {
                $table->dropColumn('is_aktif');
            }
        });
    }
};