<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // Buat ulang tabel jadwals dengan struktur baru
        Schema::dropIfExists('jadwals');
        Schema::create('jadwals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('guru_id');
            $table->unsignedBigInteger('kelas_id');
            $table->unsignedBigInteger('mapel_id');
            $table->string('hari');
            $table->integer('jam_ke'); // 1-10
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->timestamps();
            
            // Foreign keys
            $table->foreign('guru_id')->references('id')->on('guru')->onDelete('cascade');
            $table->foreign('kelas_id')->references('id')->on('kelas_master')->onDelete('cascade');
            $table->foreign('mapel_id')->references('id')->on('mapel_master')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('jadwals');
    }
};