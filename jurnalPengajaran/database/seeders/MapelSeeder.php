<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MapelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('mapel_master')->insert([

[
'kode_mapel'=>'MAT',
'nama_mapel'=>'Matematika'
],

[
'kode_mapel'=>'IPAS',
'nama_mapel'=>'Ilmu Pengetahuan Alam dan Sosial'
],

[
'kode_mapel'=>'BI',
'nama_mapel'=>'Bahasa Indonesia'
],

[
'kode_mapel'=>'BING',
'nama_mapel'=>'Bahasa Inggris'
],

[
'kode_mapel'=>'PPKn',
'nama_mapel'=>'Pendidikan Pancasila dan Kewarganegaraan '
],

[
'kode_mapel'=>'PJOK',
'nama_mapel'=>'Pendidikan Jasmani, Olahraga, dan Kesehatan'
],

[
'kode_mapel'=>'SBDP',
'nama_mapel'=>'Seni Budaya dan Prakarya'
],

[
'kode_mapel'=>'COD',
'nama_mapel'=>'Coding'
],

[
'kode_mapel'=>'BJ',
'nama_mapel'=>'Bahasa Jawa'
],

[
'kode_mapel'=>'Pankreas',
'nama_mapel'=>'Proyek Penguatan Profil Pelajar Pancasila (P5)'
],

[
'kode_mapel'=>'BA',
'nama_mapel'=>'Bahasa Arab'
],

[
'kode_mapel'=>'AA',
'nama_mapel'=>'Akidah Akhlak'
],

[
'kode_mapel'=>'FQ',
'nama_mapel'=>'Fikih'
],

[
'kode_mapel'=>'QH',
'nama_mapel'=>'Al-Qur\'an Hadis'
],

[
'kode_mapel'=>'SKI',
'nama_mapel'=>'Sejarah Kebudayaan Islam'
],

[
'kode_mapel'=>'UMI',
'nama_mapel'=>'SMetode Ummi (Al-Qur\'an)'
],
]);
    }
}
