<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->string('subject');
            $table->string('teacher');
            $table->text('topic');
            $table->text('next_topic')->nullable();
            $table->dateTime('date_time');
            $table->string('icon')->default('science');
            $table->string('color')->default('bg-primary');
            $table->boolean('is_past')->default(false);
            $table->timestamps();

            $table->foreign('student_id')
                  ->references('id')
                  ->on('students')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('activities');
    }
};