<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('kelas_master', function (Blueprint $table) {
            // Tambah kolom wali_kelas_id
            if (!Schema::hasColumn('kelas_master', 'wali_kelas_id')) {
                $table->foreignId('wali_kelas_id')->nullable()->after('nama_kelas')->constrained('guru')->nullOnDelete();
            }
            
            // Hapus kolom lama (string) jika ada
            if (Schema::hasColumn('kelas_master', 'wali_kelas')) {
                $table->dropColumn('wali_kelas');
            }
        });
    }

    public function down()
    {
        Schema::table('kelas_master', function (Blueprint $table) {
            if (Schema::hasColumn('kelas_master', 'wali_kelas_id')) {
                $table->dropForeign(['wali_kelas_id']);
                $table->dropColumn('wali_kelas_id');
            }
            if (!Schema::hasColumn('kelas_master', 'wali_kelas')) {
                $table->string('wali_kelas')->nullable();
            }
        });
    }
};