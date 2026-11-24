<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class LaporanKepatuhanExport implements FromView, ShouldAutoSize, WithStyles
{
    protected $jurnals;
    protected $teachers;

    // Terima data dari Controller
    public function __construct($jurnals, $teachers)
    {
        $this->jurnals = $jurnals;
        $this->teachers = $teachers;
    }

    public function view(): View
    {
        return view('exports.laporan-kepatuhan', [
            'jurnals' => $this->jurnals,
            'teachers' => $this->teachers
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style untuk Judul (Baris 1 & 2)
            1 => ['font' => ['bold' => true, 'size' => 16]],
            2 => ['font' => ['bold' => true, 'size' => 12]],
            
            // Style untuk Header Tabel (Baris ke-4)
            4 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FF1E3A8A'] // Warna Biru Gelap
                ],
                'alignment' => ['horizontal' => 'center']
            ],
        ];
    }
}