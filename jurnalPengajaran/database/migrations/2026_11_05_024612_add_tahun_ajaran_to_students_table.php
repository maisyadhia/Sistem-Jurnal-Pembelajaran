<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'tahun_ajaran')) {
                $table->string('tahun_ajaran', 9)->default('2026/2027')->after('class');
            }
            if (!Schema::hasColumn('students', 'status')) {
                $table->enum('status', ['aktif', 'lulus', 'pindah', 'tinggal_kelas'])
                      ->default('aktif')->after('tahun_ajaran');
            }
            if (!Schema::hasColumn('students', 'tanggal_keluar')) {
                $table->date('tanggal_keluar')->nullable()->after('status');
            }
            if (!Schema::hasColumn('students', 'keterangan')) {
                $table->text('keterangan')->nullable()->after('tanggal_keluar');
            }
        });
    }

    public function down()
    {
        Schema::table('students', function (Blueprint $table) {
            if (Schema::hasColumn('students', 'tahun_ajaran')) {
                $table->dropColumn('tahun_ajaran');
            }
            if (Schema::hasColumn('students', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('students', 'tanggal_keluar')) {
                $table->dropColumn('tanggal_keluar');
            }
            if (Schema::hasColumn('students', 'keterangan')) {
                $table->dropColumn('keterangan');
            }
        });
    }
};