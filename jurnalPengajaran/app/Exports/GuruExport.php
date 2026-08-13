<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class GuruExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return DB::table('guru')->select('kode_guru', 'nik', 'nama_guru', 'role')->get();
    }

    public function headings(): array
    {
        return ['kode_guru', 'nik', 'nama_guru', 'role'];
    }

    public function map($row): array
    {
        return [
            $row->kode_guru,
            ' ' . $row->nik, // Tambahkan spasi di depan agar dianggap teks
            $row->nama_guru,
            $row->role,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Set kolom NIK (B) sebagai teks
        $sheet->getStyle('B')->getNumberFormat()->setFormatCode('@');
        
        // Set semua sel agar tidak berubah format
        $sheet->getStyle('A:D')->getNumberFormat()->setFormatCode('@');
        
        return [
            // Bold header
            1 => ['font' => ['bold' => true]],
        ];
    }
}