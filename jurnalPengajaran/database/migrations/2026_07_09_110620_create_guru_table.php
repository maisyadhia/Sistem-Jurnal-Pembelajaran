<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guru', function (Blueprint $table) {

            $table->id();

            $table->string('nik', 20)->unique();
            $table->string('kode_guru')->unique();
            $table->string('nama_guru');
            $table->string('password');

            $table->enum('role',[
                'admin',
                'guru',
                'humas'
            ])->default('guru');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guru');
    }
};