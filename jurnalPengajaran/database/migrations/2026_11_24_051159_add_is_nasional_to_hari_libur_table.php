<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hari_libur', function (Blueprint $table) {
            // Tambahkan kolom is_nasional setelah kolom keterangan
            $table->boolean('is_nasional')->default(false)->after('keterangan');
        });
    }

    public function down(): void
    {
        Schema::table('hari_libur', function (Blueprint $table) {
            $table->dropColumn('is_nasional');
        });
    }
};