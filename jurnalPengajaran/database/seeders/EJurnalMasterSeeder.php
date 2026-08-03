<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EJurnalMasterSeeder extends Seeder
{
    public function run(): void
    {
        // 1. DATA GURU MASTER
        $teachers = [
            ['kode' => 'D',  'nip' => '19710817 199703 1 003', 'name' => 'Imam Ahmadi, M.Pd.I'],
            ['kode' => 'I',  'nip' => '19670109 199803 1 001', 'name' => 'Drs. Suyanto, M.Pd.'],
            ['kode' => 'M',  'nip' => '19711107 200501 2 004', 'name' => 'Anik Sulistyowati, S.Pd.'],
            ['kode' => 'N',  'nip' => '19680506 200701 2 035', 'name' => 'Dra. Darmini, M.Pd.'],
            ['kode' => 'T',  'nip' => '19660615 198902 2 002', 'name' => 'Dra. RA. Sukmaningtyas'],
            ['kode' => 'AC', 'nip' => '19810304 200604 1 021', 'name' => 'Zainul Arifin, S.Pd.'],
            ['kode' => 'AT', 'nip' => '19930722 201903 1 013', 'name' => 'Dwi Sulistyo Widayanto, S.Pd.'],
            ['kode' => 'AU', 'nip' => '19951124 202321 1 015', 'name' => "Ibn'Sina Farrij Karbana, S.Pd."],
            ['kode' => 'AW', 'nip' => '19940112 202221 1 012', 'name' => 'Bakhtiar Ilmi Yanuar A, S.Kom.'],
            ['kode' => 'AX', 'nip' => '19910523 202421 2 031', 'name' => 'Mukminatul Layyinah, S.Pd.'],
            ['kode' => 'AZ', 'nip' => '19780415 200801 2 016', 'name' => 'Eny Maria Andriany, S.Pd.'],
            ['kode' => 'BA', 'nip' => '19950521 201903 2 018', 'name' => 'Liamei Sinti Yanti, S.Pd.'],
            ['kode' => 'BB', 'nip' => '19901112 202221 1 009', 'name' => 'Mohammad Muizuddin Mustofa, S.Pd.'],
            ['kode' => 'BF', 'nip' => '19880914 202321 1 011', 'name' => 'Ady Irawan, S.Pd.'],
            ['kode' => 'BG', 'nip' => '19940822 202012 2 014', 'name' => "Mar'a Qonitatillah, S.Pd.I."],
            ['kode' => 'TIM', 'nip' => null, 'name' => 'Tim Guru Pengajar UMI'],
        ];

        foreach ($teachers as $t) {
            // PASTIKAN MENGGUNAKAN PETIK TUNGGAL 'guru' MURNI TANPA SIMBOL LAIN
            DB::table('guru')->insert([
                'kode_guru' => $t['kode'],
                'nip' => $t['nip'],
                'name' => $t['name'],
                'password' => Hash::make('password123'),
                'role' => 'guru',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Tambahkan Akun Humas Sekolah untuk Admin Monitoring
        DB::table('guru')->insert([
            'kode_guru' => 'ADM',
            'name' => 'Staf Humas Terpadu',
            'password' => Hash::make('admin123'),
            'role' => 'humas',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. DATA KELAS MASTER
        $classes = ['5-A' => 'B. Ella', '5-B' => 'P. Muiz', '5-C' => 'B. Sukma', '5-D' => 'B. Darmini', '5-E' => 'B. Lismei', '5-F' => 'B. Andri', '5-G' => 'B. Anik'];
        foreach ($classes as $kelas => $wali) {
            DB::table('kelas_master')->insert([
                'nama_kelas' => 'Kelas ' . $kelas,
                'wali_kelas' => $wali,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 3. DATA MATA PELAJARAN MASTER
        $subjects = [
            'BI' => 'Bahasa Indonesia', 'BING' => 'Bahasa Inggris', 'BJ' => 'Bahasa Jawa', 'BA' => 'Bahasa Arab',
            'MAT' => 'Matematika', 'IPAS' => 'Ilmu Pengetahuan Alam dan Sosial', 'PPKn' => 'Pendidikan Pancasila dan Kewarganegaraan',
            'SBDP' => 'Seni Budaya dan Prakarya', 'PJOK' => 'Pendidikan Jasmani, Olahraga, dan Kesehatan',
            'AA' => 'Akidah Akhlak', 'FQ' => 'Fikih', 'QH' => 'Al-Qur\'an Hadis', 'SKI' => 'Sejarah Kebudayaan Islam',
            'COD' => 'Coding / Komputer', 'UMI' => 'Metode Ummi (Al-Qur\'an)', 'Pankreas' => 'Proyek Penguatan Profil Pelajar Pancasila (P5)'
        ];
        foreach ($subjects as $singkatan => $nama) {
            DB::table('mapel_master')->insert([
                'singkatan' => $singkatan,
                'nama_mapel' => $nama,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}