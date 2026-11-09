<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;

class StudentSeeder extends Seeder
{
    public function run()
    {
        $students = [
            [
                'nisn' => '1234567890',
                'name' => 'Aditya Pratama',
                'dob' => '2015-05-15',
                'class' => '5A',
                'parent_name' => 'Sarah Hartono',
                'parent_phone' => '081234567890',
            ],
            [
                'nisn' => '0112235566',
                'name' => 'Mutiara Sari',
                'dob' => '2015-02-14',
                'class' => '5A',
                'parent_name' => 'Agus Setiawan',
                'parent_phone' => '081277776666',
            ],
            [
                'nisn' => '0112233445',
                'name' => 'Adi Hidayat',
                'dob' => '2015-08-20',
                'class' => '5A',
                'parent_name' => 'Bambang Hidayat',
                'parent_phone' => '081299998888',
            ],
            [
                'nisn' => '0112233447',
                'name' => 'Citra Lestari',
                'dob' => '2015-11-05',
                'class' => '5A',
                'parent_name' => 'Hendra Lestari',
                'parent_phone' => '081255554444',
            ],
            [
                'nisn' => '3142550887',
                'name' => 'ADINDA SALSABILA KHOIRIYAH',
                'dob' => '2014-12-19',
                'class' => '5A',
                'parent_name' => 'SUDIYONO',
                'parent_phone' => '082132225699',
            ],
            [
                'nisn' => '3153460348',
                'name' => 'AISHA FALGUNI FRISANTI',
                'dob' => '2015-10-12',
                'class' => '5A',
                'parent_name' => 'ANTON SOFYAN ROZIKI',
                'parent_phone' => '08113272227',
            ],
            [
                'nisn' => '3167773091',
                'name' => 'ALESHA ZAHIRA ORLIN PUTRI JOIS',
                'dob' => '2016-04-04',
                'class' => '5A',
                'parent_name' => 'JOKO SANTOSO',
                'parent_phone' => '082230152280',
            ],
            [
                'nisn' => '3159215946',
                'name' => 'ALISHA RALINDIA RAHMAH',
                'dob' => '2015-09-24',
                'class' => '5A',
                'parent_name' => 'AGUS PRIYANTO',
                'parent_phone' => '089601316008',
            ],
            [
                'nisn' => '0156667038',
                'name' => 'ARRASYA ZAHFRAN GHAISAN',
                'dob' => '2015-09-22',
                'class' => '5A',
                'parent_name' => 'NOVY KRISTANTO',
                'parent_phone' => '08569005570',
            ],
            [
                'nisn' => '3160960218',
                'name' => 'ASSHABIYA RAFIFA PRAYOGA',
                'dob' => '2016-02-15',
                'class' => '5A',
                'parent_name' => 'TRIONO HERY SUBAGYO',
                'parent_phone' => '085801562090',
            ],
            [
                'nisn' => '3156885861',
                'name' => 'AZALEA NAUVALYN PUTRI DEBY',
                'dob' => '2015-03-24',
                'class' => '5A',
                'parent_name' => 'BENY KUSMIA DEBY',
                'parent_phone' => '08121880881',
            ],
            [
                'nisn' => '3166857519',
                'name' => 'AZWA SYAQILA ROFIQI',
                'dob' => '2016-02-07',
                'class' => '5A',
                'parent_name' => 'ASIP ROFIQI, S.PD I',
                'parent_phone' => '085646618959',
            ],
            [
                'nisn' => '0153266610',
                'name' => 'BRYAN MARIO PAMUNGKAS',
                'dob' => '2015-08-01',
                'class' => '5A',
                'parent_name' => 'JOKO SANTOSO',
                'parent_phone' => '082232962878',
            ],
            [
                'nisn' => '0164291396',
                'name' => 'DILARA RUENNA YASMIN',
                'dob' => '2016-08-31',
                'class' => '5A',
                'parent_name' => 'MOH. MAS ASHAD',
                'parent_phone' => '085103465588',
            ],
        ];

        foreach ($students as $student) {
            Student::updateOrCreate(
                ['nisn' => $student['nisn']],
                array_merge($student, [
                    'tahun_ajaran' => '2026/2027',
                    'status' => 'aktif',
                ])
            );
        }
    }
}