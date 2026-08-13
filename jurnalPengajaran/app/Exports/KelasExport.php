<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class KelasExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return DB::table('kelas_master')->select('nama_kelas', 'wali_kelas')->get();
    }

    public function headings(): array
    {
        return ['nama_kelas', 'wali_kelas'];
    }
}