<?php

namespace App\Imports;

use App\Models\KelasMaster;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class KelasImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $namaKelas = trim($row['nama_kelas'] ?? '');
        
        if (empty($namaKelas)) {
            return null;
        }

        $exists = DB::table('kelas_master')
            ->where('nama_kelas', $namaKelas)
            ->exists();

        if ($exists) {
            return null;
        }

        return new KelasMaster([
            'nama_kelas' => $namaKelas,
            'wali_kelas' => $row['wali_kelas'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_kelas' => 'required',
        ];
    }
}