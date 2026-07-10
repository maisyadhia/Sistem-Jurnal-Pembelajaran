<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JadwalPelajaranSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Ambil mapping ID secara dinamis dari tabel master database
        $mapels = DB::table('mapel_master')->pluck('id', 'kode_mapel')->toArray();
        $gurus = DB::table('guru')->pluck('id', 'kode_guru')->toArray();
        
        $kelas_db = DB::table('kelas_master')->get();
        $kelas_list = [];
        foreach ($kelas_db as $k) {
            // Hilangkan kata 'Kelas ' jika ada, agar pencocokan string '5A', '5B' dst menjadi fleksibel
            $cleanName = trim(str_replace('Kelas', '', $k->nama_kelas));
            $kelas_list[$cleanName] = $k->id;
        }

        // 2. Data Jadwal Riil Paralel Kelas 5 (Senin - Kamis & Jumat) sesuai file Rekap Excel
        $raw_jadwals = [
            // ==================== HARI SENIN ====================
            // Jam ke-3 (08.10 - 08.45)
            ['hari' => 'Senin', 'jam' => 3, 'mulai' => '08:10:00', 'selesai' => '08:45:00', 'kelas' => '5A', 'mapel' => 'BING', 'guru' => 'BF'],
            ['hari' => 'Senin', 'jam' => 3, 'mulai' => '08:10:00', 'selesai' => '08:45:00', 'kelas' => '5B', 'mapel' => 'AA', 'guru' => 'BG'],
            ['hari' => 'Senin', 'jam' => 3, 'mulai' => '08:10:00', 'selesai' => '08:45:00', 'kelas' => '5C', 'mapel' => 'PPKn', 'guru' => 'M'],
            ['hari' => 'Senin', 'jam' => 3, 'mulai' => '08:10:00', 'selesai' => '08:45:00', 'kelas' => '5D', 'mapel' => 'BI', 'guru' => 'N'],
            ['hari' => 'Senin', 'jam' => 3, 'mulai' => '08:10:00', 'selesai' => '08:45:00', 'kelas' => '5E', 'mapel' => 'MAT', 'guru' => 'I'],
            ['hari' => 'Senin', 'jam' => 3, 'mulai' => '08:10:00', 'selesai' => '08:45:00', 'kelas' => '5F', 'mapel' => 'SBDP', 'guru' => 'AZ'],
            ['hari' => 'Senin', 'jam' => 3, 'mulai' => '08:10:00', 'selesai' => '08:45:00', 'kelas' => '5G', 'mapel' => 'BJ', 'guru' => 'T'],

            // Jam ke-4 (08.45 - 09.20) -> Metode Ummi Serentak
            ['hari' => 'Senin', 'jam' => 4, 'mulai' => '08:45:00', 'selesai' => '09:20:00', 'kelas' => '5A', 'mapel' => 'UMI', 'guru' => 'TIM'],
            ['hari' => 'Senin', 'jam' => 4, 'mulai' => '08:45:00', 'selesai' => '09:20:00', 'kelas' => '5B', 'mapel' => 'UMI', 'guru' => 'TIM'],
            ['hari' => 'Senin', 'jam' => 4, 'mulai' => '08:45:00', 'selesai' => '09:20:00', 'kelas' => '5C', 'mapel' => 'UMI', 'guru' => 'TIM'],
            ['hari' => 'Senin', 'jam' => 4, 'mulai' => '08:45:00', 'selesai' => '09:20:00', 'kelas' => '5D', 'mapel' => 'UMI', 'guru' => 'TIM'],
            ['hari' => 'Senin', 'jam' => 4, 'mulai' => '08:45:00', 'selesai' => '09:20:00', 'kelas' => '5E', 'mapel' => 'UMI', 'guru' => 'TIM'],
            ['hari' => 'Senin', 'jam' => 4, 'mulai' => '08:45:00', 'selesai' => '09:20:00', 'kelas' => '5F', 'mapel' => 'UMI', 'guru' => 'TIM'],
            ['hari' => 'Senin', 'jam' => 4, 'mulai' => '08:45:00', 'selesai' => '09:20:00', 'kelas' => '5G', 'mapel' => 'UMI', 'guru' => 'TIM'],

            // ==================== HARI SELASA ====================
            // Jam ke-1 (07.00 - 07.35)
            ['hari' => 'Selasa', 'jam' => 1, 'mulai' => '07:00:00', 'selesai' => '07:35:00', 'kelas' => '5A', 'mapel' => 'PJOK', 'guru' => 'AC'],
            ['hari' => 'Selasa', 'jam' => 1, 'mulai' => '07:00:00', 'selesai' => '07:35:00', 'kelas' => '5B', 'mapel' => 'BI', 'guru' => 'BB'],
            ['hari' => 'Selasa', 'jam' => 1, 'mulai' => '07:00:00', 'selesai' => '07:35:00', 'kelas' => '5C', 'mapel' => 'IPAS', 'guru' => 'BA'],
            ['hari' => 'Selasa', 'jam' => 1, 'mulai' => '07:00:00', 'selesai' => '07:35:00', 'kelas' => '5D', 'mapel' => 'MAT', 'guru' => 'N'],
            ['hari' => 'Selasa', 'jam' => 1, 'mulai' => '07:00:00', 'selesai' => '07:35:00', 'kelas' => '5E', 'mapel' => 'QH', 'guru' => 'AT'],
            ['hari' => 'Selasa', 'jam' => 1, 'mulai' => '07:00:00', 'selesai' => '07:35:00', 'kelas' => '5F', 'mapel' => 'BI', 'guru' => 'AZ'],
            ['hari' => 'Selasa', 'jam' => 1, 'mulai' => '07:00:00', 'selesai' => '07:35:00', 'kelas' => '5G', 'mapel' => 'IPAS', 'guru' => 'M'],

            // Jam ke-3 (08.10 - 08.45)
            ['hari' => 'Selasa', 'jam' => 3, 'mulai' => '08:10:00', 'selesai' => '08:45:00', 'kelas' => '5A', 'mapel' => 'MAT', 'guru' => 'D'], // Pak Imam Ahmadi mengajar 5A
            ['hari' => 'Selasa', 'jam' => 3, 'mulai' => '08:10:00', 'selesai' => '08:45:00', 'kelas' => '5B', 'mapel' => 'MAT', 'guru' => 'T'],
            ['hari' => 'Selasa', 'jam' => 3, 'mulai' => '08:10:00', 'selesai' => '08:45:00', 'kelas' => '5C', 'mapel' => 'BING', 'guru' => 'BF'],

            // ==================== HARI RABU ====================
            // Jam ke-1 (07.00 - 07.35)
            ['hari' => 'Rabu', 'jam' => 1, 'mulai' => '07:00:00', 'selesai' => '07:35:00', 'kelas' => '5A', 'mapel' => 'IPAS', 'guru' => 'I'],
            ['hari' => 'Rabu', 'jam' => 1, 'mulai' => '07:00:00', 'selesai' => '07:35:00', 'kelas' => '5B', 'mapel' => 'PJOK', 'guru' => 'AC'],
            ['hari' => 'Rabu', 'jam' => 1, 'mulai' => '07:00:00', 'selesai' => '07:35:00', 'kelas' => '5C', 'mapel' => 'BI', 'guru' => 'BA'],

            // ==================== HARI KAMIS ====================
            // Jam ke-1 (07.00 - 07.35)
            ['hari' => 'Kamis', 'jam' => 1, 'mulai' => '07:00:00', 'selesai' => '07:35:00', 'kelas' => '5A', 'mapel' => 'MAT', 'guru' => 'D'], // Pak Imam Ahmadi mengajar 5A
            ['hari' => 'Kamis', 'jam' => 1, 'mulai' => '07:00:00', 'selesai' => '07:35:00', 'kelas' => '5B', 'mapel' => 'IPAS', 'guru' => 'BB'],
            
            // Jam ke-5 (09.35 - 10.10) -> Sesi Projek P5 (Pankreas)
            ['hari' => 'Kamis', 'jam' => 5, 'mulai' => '09:35:00', 'selesai' => '10:10:00', 'kelas' => '5A', 'mapel' => 'Pankreas', 'guru' => 'D'], // Pak Imam Ahmadi mengajar 5A
            ['hari' => 'Kamis', 'jam' => 5, 'mulai' => '09:35:00', 'selesai' => '10:10:00', 'kelas' => '5C', 'mapel' => 'Pankreas', 'guru' => 'T'],
            ['hari' => 'Kamis', 'jam' => 5, 'mulai' => '09:35:00', 'selesai' => '10:10:00', 'kelas' => '5D', 'mapel' => 'Pankreas', 'guru' => 'N'],

            // ==================== HARI JUMAT ====================
            // Jam ke-3 (08.20 - 08.55) -> Penyesuaian Jam Khusus Hari Jumat
            ['hari' => 'Jumat', 'jam' => 3, 'mulai' => '08:20:00', 'selesai' => '08:55:00', 'kelas' => '5A', 'mapel' => 'SKI', 'guru' => 'AU'],
            ['hari' => 'Jumat', 'jam' => 3, 'mulai' => '08:20:00', 'selesai' => '08:55:00', 'kelas' => '5B', 'mapel' => 'IPAS', 'guru' => 'BB'],
            ['hari' => 'Jumat', 'jam' => 3, 'mulai' => '08:20:00', 'selesai' => '08:55:00', 'kelas' => '5F', 'mapel' => 'MAT', 'guru' => 'AX'],
        ];

        // 3. Eksekusi penyimpanan ke tabel jadwals dengan proteksi keabsahan ID master
        foreach ($raw_jadwals as $rj) {
            $keyKelas = trim(str_replace('Kelas', '', $rj['kelas']));
            
            if (isset($gurus[$rj['guru']]) && isset($kelas_list[$keyKelas]) && isset($mapels[$rj['mapel']])) {
                DB::table('jadwals')->insert([
                    'guru_id' => $gurus[$rj['guru']],
                    'kelas_id' => $kelas_list[$keyKelas],
                    'mapel_id' => $mapels[$rj['mapel']],
                    'hari' => $rj['hari'],
                    'jam_ke' => $rj['jam'],
                    'jam_mulai' => $rj['mulai'],
                    'jam_selesai' => $rj['selesai'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}