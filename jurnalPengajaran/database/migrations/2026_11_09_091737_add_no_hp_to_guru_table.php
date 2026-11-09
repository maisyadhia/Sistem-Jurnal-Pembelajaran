<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('guru', function (Blueprint $table) {
            if (!Schema::hasColumn('guru', 'no_hp')) {
                $table->string('no_hp', 20)->nullable()->after('nama_guru');
            }
        });
    }

    public function down()
    {
        Schema::table('guru', function (Blueprint $table) {
            if (Schema::hasColumn('guru', 'no_hp')) {
                $table->dropColumn('no_hp');
            }
        });
    }
};