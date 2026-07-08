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
            $table->integer('teacher_id');
            $table->date('date');
            $table->string('class');
            $table->string('subject');
            $table->string('time');
            $table->text('topic');
            $table->text('next_target');
            $table->boolean('rpp_completed')->default(false);
            $table->boolean('absent_students')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('jurnals');
    }
};