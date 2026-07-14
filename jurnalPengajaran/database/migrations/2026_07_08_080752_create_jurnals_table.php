<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('jurnals', function (Blueprint $table) {
            $table->id();
            
            // Relasi Identitas Pengajaran
            $table->unsignedBigInteger('guru_id');
            $table->unsignedBigInteger('kelas_id');
            $table->unsignedBigInteger('mapel_id');
            
            
            // Detail Informasi Pengajaran
            $table->integer('jam_ke')->default(1);
            $table->text('materi'); // Menampung isi 'topic'
            $table->text('target_next'); // Menampung isi 'next_target'
            
            // Status Checkbox Utama
            $table->boolean('rpp_sesuai')->default(false); // Menampung 'rpp_completed'
            $table->boolean('ada_absen')->default(false); // Menampung 'absent_students'
            
            // Informasi Waktu
            $table->date('tanggal');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('jurnals');
    }
};