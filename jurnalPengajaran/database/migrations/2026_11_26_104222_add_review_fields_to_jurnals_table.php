<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jurnals', function (Blueprint $table) {
            // Cek dulu apakah kolom sudah ada (untuk berjaga-jaga)
            if (!Schema::hasColumn('jurnals', 'status_review')) {
                $table->enum('status_review', ['pending', 'disetujui', 'revisi'])
                      ->default('pending')
                      ->after('materi'); // Sesuaikan posisi kolom
            }
            if (!Schema::hasColumn('jurnals', 'catatan_admin')) {
                $table->text('catatan_admin')->nullable()->after('status_review');
            }
        });
    }

    public function down(): void
    {
        Schema::table('jurnals', function (Blueprint $table) {
            if (Schema::hasColumn('jurnals', 'status_review')) {
                $table->dropColumn('status_review');
            }
            if (Schema::hasColumn('jurnals', 'catatan_admin')) {
                $table->dropColumn('catatan_admin');
            }
        });
    }
};