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
                'dob' => '2007-05-15',
                'class' => 'Kelas XI - IPA 2',
                'parent_name' => 'Sarah Hartono',
                'parent_phone' => '081234567890',
            ]
        );
    }
}