<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwals', function (Blueprint $table) {

            $table->id();

            $table->foreignId('guru_id')
                    ->constrained('guru')
                    ->cascadeOnDelete();

            $table->foreignId('kelas_id')
                    ->constrained('kelas_master')
                    ->cascadeOnDelete();

            $table->foreignId('mapel_id')
                    ->constrained('mapel_master')
                    ->cascadeOnDelete();

            $table->string('hari');

            $table->integer('jam_ke');

            $table->time('jam_mulai');

            $table->time('jam_selesai');

            $table->timestamps();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwals');
    }
};