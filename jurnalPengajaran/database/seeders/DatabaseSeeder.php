<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Jalankan seeder baru kita yang sudah sinkron dengan jadwal Excel
        $this->call([
            GuruSeeder::class,
            KelasSeeder::class,
            MapelSeeder::class,
            JadwalPelajaranSeeder::class,
        ]);
        
        // 2. Disable foreign key checks untuk proses pembersihan data lama
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Truncate tabel-tabel bawaan agar bersih
        if (class_exists(\App\Models\Jurnal::class)) \App\Models\Jurnal::truncate();
        if (class_exists(\App\Models\Note::class)) \App\Models\Note::truncate();
        if (class_exists(\App\Models\Activity::class)) \App\Models\Activity::truncate();
        if (class_exists(\App\Models\UnreportedClass::class)) \App\Models\UnreportedClass::truncate();
        if (class_exists(\App\Models\Student::class)) \App\Models\Student::truncate();
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // 3. Panggil seeder pelengkap lainnya yang tidak tabrakan
        // CATATAN: DataMasterSeeder KITA MATIKAN/HAPUS karena sudah digantikan oleh EJurnalMasterSeeder
        $this->call([
            AdminSeeder::class,
            StudentSeeder::class,
            UnreportedClassSeeder::class,
            JurnalSeeder::class,
        ]);

        $this->command->info('✅ Database seeded successfully!');
    }
}