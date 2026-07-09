<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\UnreportedClass;

class UnreportedClassSeeder extends Seeder
{
    public function run()
    {
        $unreportedClasses = [
            [
                'code' => 'XII-A',
                'subject' => 'Matematika Peminatan',
                'teacher' => 'Bpk. Heru Setiawan',
                'schedule' => 'Jam ke 1-3',
                'date' => today(),
                'reported' => false,
            ],
            [
                'code' => 'X-F',
                'subject' => 'Bahasa Inggris',
                'teacher' => 'Ibu Maya Sari',
                'schedule' => 'Jam ke 4-5',
                'date' => today(),
                'reported' => false,
            ],
            [
                'code' => 'XI-C',
                'subject' => 'Fisika',
                'teacher' => 'Drs. Ahmad Junaidi',
                'schedule' => 'Jam ke 1-2',
                'date' => today(),
                'reported' => false,
            ],
        ];

        foreach ($unreportedClasses as $class) {
            UnreportedClass::updateOrCreate(
                ['code' => $class['code'], 'date' => $class['date']],
                $class
            );
        }
    }
}