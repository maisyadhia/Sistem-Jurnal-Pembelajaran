<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataMaster;

class DataMasterSeeder extends Seeder
{
    public function run()
    {
        $dataMasters = [
            // Guru
            [
                'name' => 'Imam Ahmadi, M.Pd.I',
                'identifier' => 'NIP. 197108171997031003',
                'initials' => 'IA',
                'category' => 'GURU TETAP',
                'status' => 'Aktif',
                'statusColor' => 'bg-secondary',
                'color' => 'bg-secondary-container',
            ],
            [
                'name' => 'Drs. Suyanto, M.Pd',
                'identifier' => 'NIP. 196701091998031001',
                'initials' => 'SY',
                'category' => 'GURU TETAP',
                'status' => 'Aktif',
                'statusColor' => 'bg-secondary',
                'color' => 'bg-primary-container',
            ],
            [
                'name' => 'Anik Sulistyowati, S.Pd',
                'identifier' => 'NIP. 197111072005012004',
                'initials' => 'AS',
                'category' => 'GURU TETAP',
                'status' => 'Aktif',
                'statusColor' => 'bg-secondary',
                'color' => 'bg-tertiary-container',
            ],
            [
                'name' => 'Dra. Darmini, M.Pd',
                'identifier' => 'NIP. 196805062007012035',
                'initials' => 'DR',
                'category' => 'GURU TETAP',
                'status' => 'Aktif',
                'statusColor' => 'bg-secondary',
                'color' => 'bg-primary',
            ],
            [
                'name' => 'Dra. RA. Sukmaningtyas',
                'identifier' => 'NIP. 196606151989022002',
                'initials' => 'RS',
                'category' => 'GURU TETAP',
                'status' => 'Aktif',
                'statusColor' => 'bg-secondary',
                'color' => 'bg-secondary-container',
            ],
            // Kelas
            [
                'name' => 'Kelas 5A',
                'identifier' => 'Lantai 1, Gedung A',
                'initials' => '5A',
                'category' => 'INFRASTRUKTUR',
                'status' => 'Tersedia',
                'statusColor' => 'bg-secondary',
                'color' => 'bg-primary-container',
            ],
            [
                'name' => 'Kelas 5B',
                'identifier' => 'Lantai 1, Gedung A',
                'initials' => '5B',
                'category' => 'INFRASTRUKTUR',
                'status' => 'Tersedia',
                'statusColor' => 'bg-secondary',
                'color' => 'bg-tertiary-container',
            ],
            [
                'name' => 'Kelas 5C',
                'identifier' => 'Lantai 1, Gedung B',
                'initials' => '5C',
                'category' => 'INFRASTRUKTUR',
                'status' => 'Tersedia',
                'statusColor' => 'bg-secondary',
                'color' => 'bg-primary',
            ],
            [
                'name' => 'Kelas 5D',
                'identifier' => 'Lantai 1, Gedung B',
                'initials' => '5D',
                'category' => 'INFRASTRUKTUR',
                'status' => 'Tersedia',
                'statusColor' => 'bg-secondary',
                'color' => 'bg-secondary-container',
            ],
            [
                'name' => 'Kelas 5E',
                'identifier' => 'Lantai 2, Gedung A',
                'initials' => '5E',
                'category' => 'INFRASTRUKTUR',
                'status' => 'Tersedia',
                'statusColor' => 'bg-secondary',
                'color' => 'bg-primary-container',
            ],
            [
                'name' => 'Kelas 5F',
                'identifier' => 'Lantai 2, Gedang A',
                'initials' => '5F',
                'category' => 'INFRASTRUKTUR',
                'status' => 'Tersedia',
                'statusColor' => 'bg-secondary',
                'color' => 'bg-tertiary-container',
            ],
            [
                'name' => 'Kelas 5G',
                'identifier' => 'Lantai 2, Gedung B',
                'initials' => '5G',
                'category' => 'INFRASTRUKTUR',
                'status' => 'Tersedia',
                'statusColor' => 'bg-secondary',
                'color' => 'bg-primary',
            ],
            // Mata Pelajaran
            [
                'name' => 'Matematika',
                'identifier' => 'Kurikulum Merdeka',
                'initials' => 'MAT',
                'category' => 'MATA PELAJARAN',
                'status' => 'Aktif',
                'statusColor' => 'bg-secondary',
                'color' => 'bg-secondary-container',
            ],
            [
                'name' => 'Bahasa Indonesia',
                'identifier' => 'Kurikulum Merdeka',
                'initials' => 'BI',
                'category' => 'MATA PELAJARAN',
                'status' => 'Aktif',
                'statusColor' => 'bg-secondary',
                'color' => 'bg-primary-container',
            ],
            [
                'name' => 'Bahasa Inggris',
                'identifier' => 'Kurikulum Merdeka',
                'initials' => 'BING',
                'category' => 'MATA PELAJARAN',
                'status' => 'Aktif',
                'statusColor' => 'bg-secondary',
                'color' => 'bg-tertiary-container',
            ],
        ];

        foreach ($dataMasters as $data) {
            DataMaster::updateOrCreate(
                ['identifier' => $data['identifier']],
                $data
            );
        }
    }
}