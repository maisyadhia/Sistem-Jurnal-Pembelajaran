<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class KelasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('kelas_master')->insert([
            ['nama_kelas'=>'5A'],
            ['nama_kelas'=>'5B'],
            ['nama_kelas'=>'5C'],
            ['nama_kelas'=>'5D'],
            ['nama_kelas'=>'5E'],
            ['nama_kelas'=>'5F'],
            ['nama_kelas'=>'5G'],

        ]);
    }
}
