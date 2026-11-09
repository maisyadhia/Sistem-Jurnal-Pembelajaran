<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('nilai_asat')) {
            Schema::create('nilai_asat', function (Blueprint $table) {
                $table->id();
                $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
                $table->string('tahun_ajaran', 9);
                $table->string('kelas', 10);
                $table->decimal('nilai_total', 5, 2)->default(0);
                $table->decimal('nilai_bind', 5, 2)->nullable();
                $table->decimal('nilai_mat', 5, 2)->nullable();
                $table->decimal('nilai_ipa', 5, 2)->nullable();
                $table->text('catatan')->nullable();
                $table->timestamps();
                
                $table->index(['student_id', 'tahun_ajaran']);
                $table->index(['tahun_ajaran', 'kelas']);
                $table->index('nilai_total');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('nilai_asat');
    }
};