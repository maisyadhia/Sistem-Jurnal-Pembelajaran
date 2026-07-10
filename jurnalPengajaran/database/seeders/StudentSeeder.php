<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;

class StudentSeeder extends Seeder
{
    public function run()
    {
        // Create default student for parent login
        Student::updateOrCreate(
            ['nisn' => '1234567890'],
            [
                'name' => 'Aditya Pratama',
                'dob' => '2015-05-15',
                'class' => '5A',
                'parent_name' => 'Sarah Hartono',
                'parent_phone' => '081234567890',
            ]
        );

        Student::updateOrCreate(
            ['nisn' => '0112235566'],
            [
                'name' => 'Mutiara Sari',
                'dob' => '2015-02-14',
                'class' => '5A',
                'parent_name' => 'Agus Setiawan',
                'parent_phone' => '081277776666',
            ]
        );

        Student::updateOrCreate(
            ['nisn' => '0112233445'],
            [
                'name' => 'Adi Hidayat',
                'dob' => '2015-08-20',
                'class' => '5A',
                'parent_name' => 'Bambang Hidayat',
                'parent_phone' => '081299998888',
            ]
        );

        Student::updateOrCreate(
            ['nisn' => '0112233447'],
            [
                'name' => 'Citra Lestari',
                'dob' => '2015-11-05',
                'class' => '5A',
                'parent_name' => 'Hendra Lestari',
                'parent_phone' => '081255554444',
            ]
        );
    }
}