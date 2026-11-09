<?php

namespace App\Exports;

use App\Models\Student;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class SiswaGenderExport implements FromCollection, WithHeadings, WithMapping
{
    protected $tahunAjaran;

    public function __construct($tahunAjaran)
    {
        $this->tahunAjaran = $tahunAjaran;
    }

    public function collection()
    {
        return Student::where('tahun_ajaran', $this->tahunAjaran)
            ->where('status', 'aktif')
            ->orderBy('class')
            ->orderBy('name')
            ->get();
    }

    public function headings(): array
    {
        return ['nisn', 'nama', 'jenis_kelamin', 'kelas'];
    }

    public function map($row): array
    {
        return [
            $row->nisn,
            $row->name,
            $row->gender ?? '',
            $row->class,
        ];
    }
}