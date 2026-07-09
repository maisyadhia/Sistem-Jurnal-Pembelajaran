<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DataMaster;

class DataMasterSeeder extends Seeder
{
    public function run()
    {
        $dataMasters = [
            [
                'name' => 'Nanang Sukmawan S., S.Pd., M.Pd.I.',
                'identifier' => 'NIP. 197811272005011002',
                'initials' => 'NS',
                'category' => 'GURU TETAP',
                'status' => 'Aktif',
                'statusColor' => 'bg-secondary',
                'color' => 'bg-secondary-container',
            ],
            [
                'name' => 'Lilik Fauziyah, M.Pd',
                'identifier' => 'NIP. 198301062006042012',
                'initials' => 'LF',
                'category' => 'GURU TETAP',
                'status' => 'Aktif',
                'statusColor' => 'bg-secondary',
                'color' => 'bg-primary-container',
            ],
            [
                'name' => 'Ruang Kelas 5A',
                'identifier' => 'Lantai 2, Gedung B',
                'initials' => '5A',
                'category' => 'INFRASTRUKTUR',
                'status' => 'Tersedia',
                'statusColor' => 'bg-secondary',
                'color' => 'bg-tertiary-container',
            ],
            [
                'name' => 'Matematika',
                'identifier' => 'Kurikulum Merdeka',
                'initials' => 'MTK',
                'category' => 'MATA PELAJARAN',
                'status' => 'Aktif',
                'statusColor' => 'bg-secondary',
                'color' => 'bg-primary',
            ],
            [
                'name' => 'Bahasa Indonesia',
                'identifier' => 'Kurikulum Merdeka',
                'initials' => 'BI',
                'category' => 'MATA PELAJARAN',
                'status' => 'Aktif',
                'statusColor' => 'bg-secondary',
                'color' => 'bg-secondary-container',
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