<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('data_masters', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('identifier');
            $table->string('initials', 3);
            $table->string('category');
            $table->string('status');
            $table->string('statusColor')->default('bg-secondary');
            $table->string('color')->default('bg-secondary-container');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('data_masters');
    }
};