<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GuruSeeder extends Seeder
{
    public function run(): void
    {

        DB::table('guru')->insert([

                [
                    'kode_guru'=>'D',
                    'nik'=>'199107312025051003',
                    'nama_guru'=>'Imam Ahmadi, M.Pd.i',
                    'password'=>bcrypt('guru1'),
                    'role'=>'guru'
                ],

               [
                    'kode_guru'=>'I',
                    'nik'=>'199107312025051004',
                    'nama_guru'=>'Drs. Suyanto, M.Pd',
                    'password'=>bcrypt('guru2'),
                    'role'=>'guru'
               ],
               [
                    'kode_guru'=>'M',
                    'nik'=>'199107312025051005',
                    'nama_guru'=>'Anik Sulistyowati, S.Pd',
                    'password'=>bcrypt('guru3'),
                    'role'=>'guru'
               ],

                [
                    'kode_guru'=>'N',
                    'nik'=>'199107312025051006',
                    'nama_guru'=>'Dra. Darmini, M.Pd',
                    'password'=>bcrypt('guru4'),
                    'role'=>'guru'
                ],

                [
                    'kode_guru'=>'T',
                    'nik'=>'199107312025051007',
                    'nama_guru'=>'Dra. RA. Sukmaningtyas',
                    'password'=>bcrypt('guru5'),
                    'role'=>'guru'
                ],

                [
                    'kode_guru'=>'AC',
                    'nik'=>'199107312025051008',
                    'nama_guru'=>'Zainul Arifin, S.Pd',
                    'password'=>bcrypt('guru6'),
                    'role'=>'guru'
                ],

                [
                    'kode_guru'=>'AT',
                    'nik'=>'199107312025051009',
                    'nama_guru'=>'Dwi Sulistyo Widayanto, S.Pd',
                    'password'=>bcrypt('guru7'),
                    'role'=>'guru'
                ],

                [
                    'kode_guru'=>'AU',
                    'nik'=>'199107312025051010',
                    'nama_guru'=>'Ibn\'Sina Farrij Karbana, S.Pd',
                    'password'=>bcrypt('guru8'),
                    'role'=>'guru'
                ],
                [
                    'kode_guru'=>'AW',
                    'nik'=>'199107312025051011',
                    'nama_guru'=>'Bakhtiar Ilmi Yanuar A, S.Kom',
                    'password'=>bcrypt('guru9'),
                    'role'=>'guru'
                ],
                 [
                    'kode_guru'=>'AX',
                    'nik'=>'199107312025051012',
                    'nama_guru'=>'Mukminatul Layyinah, S.Pd',
                    'password'=>bcrypt('guru10'),
                    'role'=>'guru'
                ],
                 [
                    'kode_guru'=>'AZ',
                    'nik'=>'199107312025051013',
                    'nama_guru'=>'Eny Maria Andriany, S.Pd',
                    'password'=>bcrypt('guru11'),
                    'role'=>'guru'
                ],
                 [
                    'kode_guru'=>'BA',
                    'nik'=>'199107312025051014',
                    'nama_guru'=>'Liamei Sinti Yanti, S.Pd',
                    'password'=>bcrypt('guru12'),
                    'role'=>'guru'
                ],
                 [
                    'kode_guru'=>'BB',
                    'nik'=>'199107312025051015',
                    'nama_guru'=>'Mohammad Muizuddin Mustofa, S.Pd.',
                    'password'=>bcrypt('guru13'),
                    'role'=>'guru'
                ],
                 [
                    'kode_guru'=>'BF',
                    'nik'=>'199107312025051016',
                    'nama_guru'=>'Ady Irawan, S.Pd',
                    'password'=>bcrypt('guru14'),
                    'role'=>'guru'
                ],
                 [
                    'kode_guru'=>'BG',
                    'nik'=>'199107312025051017',
                    'nama_guru'=>'Mar\'a Qonitatillah, S.Pd.I',
                    'password'=>bcrypt('guru15'),
                    'role'=>'guru'
                ],


        ]);

    }
}