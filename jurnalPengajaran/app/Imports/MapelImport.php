<?php

namespace App\Imports;

use App\Models\MapelMaster;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class MapelImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $kodeMapel = trim($row['kode_mapel'] ?? '');
        $namaMapel = trim($row['nama_mapel'] ?? '');
        
        if (empty($kodeMapel) || empty($namaMapel)) {
            return null;
        }

        $exists = DB::table('mapel_master')
            ->where('kode_mapel', $kodeMapel)
            ->orWhere('nama_mapel', $namaMapel)
            ->exists();

        if ($exists) {
            return null;
        }

        return new MapelMaster([
            'kode_mapel' => $kodeMapel,
            'nama_mapel' => $namaMapel,
        ]);
    }

    public function rules(): array
    {
        return [
            'kode_mapel' => 'required',
            'nama_mapel' => 'required',
        ];
    }
}