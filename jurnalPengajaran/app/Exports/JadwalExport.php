<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class JadwalExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles
{
    public function collection()
    {
        return DB::table('jadwals')
            ->join('guru', 'jadwals.guru_id', '=', 'guru.id')
            ->join('kelas_master', 'jadwals.kelas_id', '=', 'kelas_master.id')
            ->join('mapel_master', 'jadwals.mapel_id', '=', 'mapel_master.id')
            ->select(
                'guru.kode_guru',
                'kelas_master.nama_kelas as kelas',
                'mapel_master.kode_mapel',
                'jadwals.hari',
                'jadwals.jam_ke',
                'jadwals.jam_mulai',
                'jadwals.jam_selesai'
            )
            ->get();
    }

    public function headings(): array
    {
        return ['kode_guru', 'kelas', 'kode_mapel', 'hari', 'jam_ke', 'jam_mulai', 'jam_selesai'];
    }

    public function map($row): array
    {
        // 🔥 FORMAT WAKTU KE HH:MM (tanpa detik)
        $jamMulai = $row->jam_mulai ? substr($row->jam_mulai, 0, 5) : '00:00';
        $jamSelesai = $row->jam_selesai ? substr($row->jam_selesai, 0, 5) : '00:00';

        return [
            $row->kode_guru,
            $row->kelas,
            $row->kode_mapel,
            $row->hari,
            $row->jam_ke,
            $jamMulai,
            $jamSelesai,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Set semua kolom sebagai teks agar format tidak berubah
        $sheet->getStyle('A:G')->getNumberFormat()->setFormatCode('@');
        
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}