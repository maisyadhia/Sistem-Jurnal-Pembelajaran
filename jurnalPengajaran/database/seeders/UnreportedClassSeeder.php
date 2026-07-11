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
                'code' => '5A',
                'subject' => 'Matematika',
                'teacher' => 'Imam Ahmadi, M.Pd.I',
                'schedule' => 'Jam ke 1-2',
                'date' => today(),
                'reported' => false,
            ],
            [
                'code' => '5B',
                'subject' => 'Bahasa Indonesia',
                'teacher' => 'Dra. Darmini, M.Pd',
                'schedule' => 'Jam ke 3-4',
                'date' => today(),
                'reported' => false,
            ],
            [
                'code' => '5C',
                'subject' => 'IPA',
                'teacher' => 'Drs. Suyanto, M.Pd',
                'schedule' => 'Jam ke 1-2',
                'date' => today(),
                'reported' => false,
            ],
            [
                'code' => '5D',
                'subject' => 'Bahasa Inggris',
                'teacher' => 'Ady Irawan, S.Pd',
                'schedule' => 'Jam ke 4-5',
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