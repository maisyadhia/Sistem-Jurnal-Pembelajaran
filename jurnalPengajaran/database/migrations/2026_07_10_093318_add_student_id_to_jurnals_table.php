<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up(): void
{
    Schema::table('jurnals', function (Blueprint $table) {
        // Mengubah menjadi json agar bisa menyimpan banyak ID murid sekaligus woy
        $table->json('student_ids')->nullable();
    });
}

public function down(): void
{
    Schema::table('jurnals', function (Blueprint $table) {
        $table->dropColumn('student_ids');
    });
}
};
