<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Student;
use App\Models\Activity;
use App\Models\Note;
use App\Models\UnreportedClass;
use App\Models\DataMaster;
use App\Models\Jurnal;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Truncate tables in correct order
        Jurnal::truncate();
        Note::truncate();
        Activity::truncate();
        DataMaster::truncate();
        UnreportedClass::truncate();
        Student::truncate();
        
        // Enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        // Create Students
        $student = Student::create([
            'nisn' => '1234567890',
            'name' => 'Aditya Pratama',
            'dob' => '2007-05-15',
            'class' => 'Kelas XI - IPA 2',
            'parent_name' => 'Sarah Hartono',
            'parent_phone' => '081234567890',
        ]);

        // Create Activities
        $activities = [
            [
                'student_id' => $student->id,
                'subject' => 'Biologi',
                'teacher' => 'Dra. Mulyani',
                'topic' => 'Struktur dan Fungsi Jaringan Tumbuhan',
                'next_topic' => 'Fotosintesis: Reaksi Terang & Gelap',
                'date_time' => now()->setTime(8, 0),
                'icon' => 'science',
                'color' => 'bg-primary',
                'is_past' => false,
            ],
            [
                'student_id' => $student->id,
                'subject' => 'Matematika Peminatan',
                'teacher' => 'Hadi Wijaya, M.Pd',
                'topic' => 'Persamaan Trigonometri Dasar',
                'next_topic' => 'Latihan Soal & Kuis Mandiri',
                'date_time' => now()->setTime(10, 0),
                'icon' => 'calculate',
                'color' => 'bg-tertiary',
                'is_past' => false,
            ],
            [
                'student_id' => $student->id,
                'subject' => 'Bahasa Indonesia',
                'teacher' => 'Siti Aminah, S.Pd',
                'topic' => 'Analisis Struktur Teks Eksplanasi',
                'next_topic' => null,
                'date_time' => now()->subDay()->setTime(8, 0),
                'icon' => 'history_edu',
                'color' => 'bg-outline',
                'is_past' => true,
            ],
        ];

        foreach ($activities as $activity) {
            Activity::create($activity);
        }

        // Create Note
        Note::create([
            'student_id' => $student->id,
            'content' => 'Aditya menunjukkan peningkatan signifikan dalam partisipasi diskusi kelas Biologi. Pertahankan fokusnya.',
            'teacher' => 'Drs. Bambang S.',
        ]);

        // Create Unreported Classes
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
            UnreportedClass::create($class);
        }

        // Create Data Master
        $dataMasters = [
            [
                'name' => 'Gelar Arfian, M.Pd',
                'identifier' => 'NIP. 19880211002',
                'initials' => 'GA',
                'category' => 'GURU TETAP',
                'status' => 'Aktif',
                'statusColor' => 'bg-secondary',
                'color' => 'bg-secondary-container',
            ],
            [
                'name' => 'Ruang Kelas X-A',
                'identifier' => 'Lantai 2, Gedung B',
                'initials' => 'X-A',
                'category' => 'INFRASTRUKTUR',
                'status' => 'Tersedia',
                'statusColor' => 'bg-secondary',
                'color' => 'bg-primary-container',
            ],
            [
                'name' => 'Matematika Wajib',
                'identifier' => 'Kurikulum Merdeka',
                'initials' => 'MTK',
                'category' => 'MATA PELAJARAN',
                'status' => 'Ditinjau',
                'statusColor' => 'bg-on-tertiary-container',
                'color' => 'bg-tertiary-container',
            ],
        ];

        foreach ($dataMasters as $data) {
            DataMaster::create($data);
        }

        // Create Jurnal
        Jurnal::create([
            'teacher_id' => 1,
            'date' => today(),
            'class' => 'Kelas 10-A',
            'subject' => 'Matematika',
            'time' => '08:00 - 09:30',
            'topic' => 'Pengenalan Logaritma: Sifat-sifat dasar logaritma dan hubungannya dengan eksponen. Siswa mengerjakan latihan mandiri hal 42.',
            'next_target' => 'Penerapan logaritma dalam perhitungan bunga majemuk dan kuis kecil materi eksponen.',
            'rpp_completed' => true,
            'absent_students' => false,
        ]);

        $this->command->info('✅ Database seeded successfully!');
        $this->command->info('');
        $this->command->info('📝 Login credentials:');
        $this->command->info('  👨‍👩‍👦 Wali Murid: NISN=1234567890, DOB=2007-05-15');
        $this->command->info('  👨‍💼 Admin: NISN=admin, DOB=admin123');
        $this->command->info('  👨‍🏫 Guru: NISN=guru, DOB=guru123');
    }
}