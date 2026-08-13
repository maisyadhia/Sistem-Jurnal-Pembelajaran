<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SiswaExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return DB::table('students')->select('nisn', 'name', 'dob', 'class', 'parent_name', 'parent_phone')->get();
    }

    public function headings(): array
    {
        return ['nisn', 'nama', 'tanggal_lahir', 'kelas', 'orang_tua', 'no_telepon'];
    }

    public function map($row): array
    {
        return [
            $row->nisn,
            $row->name,
            $row->dob,
            $row->class,
            $row->parent_name,
            $row->parent_phone,
        ];
    }
}