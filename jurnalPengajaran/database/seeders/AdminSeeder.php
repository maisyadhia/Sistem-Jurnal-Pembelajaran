<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $admins = [
            [
                'nik' => '197811272005011002',
                'name' => 'Nanang Sukmawan S., S.Pd., M.Pd.I.',
                'email' => 'nanang@min2malang.sch.id',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'phone' => '081234567890',
                'position' => 'Kepala Madrasah',
            ],
            [
                'nik' => '198301062006042012',
                'name' => 'Lilik Fauziyah, M.Pd',
                'email' => 'lilik@min2malang.sch.id',
                'password' => Hash::make('humas123'),
                'role' => 'humas',
                'phone' => '081234567891',
                'position' => 'Korbid Kurikulum',
            ],
        ];

        foreach ($admins as $admin) {
            Admin::updateOrCreate(
                ['nik' => $admin['nik']],
                $admin
            );
        }
    }
}