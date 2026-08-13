<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class MapelExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return DB::table('mapel_master')->select('kode_mapel', 'nama_mapel')->get();
    }

    public function headings(): array
    {
        return ['kode_mapel', 'nama_mapel'];
    }
}