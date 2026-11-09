<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('student_class_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('tahun_ajaran', 9);
            $table->string('kelas');
            $table->enum('status', ['naik', 'tinggal', 'lulus', 'pindah'])->default('naik');
            $table->text('keterangan')->nullable();
            $table->timestamps();
            
            $table->index(['student_id', 'tahun_ajaran']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('student_class_histories');
    }
};