<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('unreported_classes', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('subject');
            $table->string('teacher');
            $table->string('schedule');
            $table->date('date');
            $table->boolean('reported')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('unreported_classes');
    }
};