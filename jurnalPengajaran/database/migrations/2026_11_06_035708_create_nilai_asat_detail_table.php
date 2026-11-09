<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('nilai_asat_detail', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nilai_asat_id')->constrained('nilai_asat')->onDelete('cascade');
            $table->foreignId('mapel_id')->constrained('mapel_master')->onDelete('cascade');
            $table->decimal('nilai', 5, 2);
            $table->timestamps();
            
            $table->index(['nilai_asat_id', 'mapel_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('nilai_asat_detail');
    }
};