<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Truncate tables in correct order
        \App\Models\Jurnal::truncate();
        \App\Models\Note::truncate();
        \App\Models\Activity::truncate();
        \App\Models\DataMaster::truncate();
        \App\Models\UnreportedClass::truncate();
        \App\Models\Student::truncate();
        
        // Enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Call seeders
        $this->call([
            AdminSeeder::class,
            StudentSeeder::class,
            DataMasterSeeder::class,
            UnreportedClassSeeder::class,
        ]);

        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('');
        $this->command->info('📝 Login credentials:');
        $this->command->info('  👨‍💼 Admin (Kepala Madrasah): NIK=197811272005011002, Password=admin123');
        $this->command->info('  👨‍💼 Humas: NIK=198301062006042012, Password=humas123');
        $this->command->info('  👨‍🏫 Guru: NIK=199107312025051003, Password=guru123');
        $this->command->info('  👨‍👩‍👦 Wali Murid: NISN=1234567890, DOB=2007-05-15');
    }
}