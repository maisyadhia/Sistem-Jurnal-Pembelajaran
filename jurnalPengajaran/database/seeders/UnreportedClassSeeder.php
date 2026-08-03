<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnreportedClassSeeder extends Seeder
{
    public function run()
    {
        // Hapus data lama
        DB::table('unreported_classes')->truncate();

        $unreported = [
            [
                'code' => '5A',
                'subject' => 'Matematika',
                'teacher' => 'Imam Ahmadi, M.Pd.I',
                'schedule' => 'Senin, 07:00 - 07:35',
                'date' => today(),
                'reported' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => '5B',
                'subject' => 'Bahasa Indonesia',
                'teacher' => 'Drs. Suyanto, M.Pd',
                'schedule' => 'Senin, 08:10 - 08:45',
                'date' => today(),
                'reported' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => '5C',
                'subject' => 'Bahasa Inggris',
                'teacher' => 'Anik Sulistyowati, S.Pd',
                'schedule' => 'Selasa, 07:00 - 07:35',
                'date' => today(),
                'reported' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        foreach ($unreported as $data) {
            DB::table('unreported_classes')->insert($data);
        }
    }
}