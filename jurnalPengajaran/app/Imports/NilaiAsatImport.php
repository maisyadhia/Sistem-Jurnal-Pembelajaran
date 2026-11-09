<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\NilaiAsat;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class NilaiAsatImport extends DefaultValueBinder implements ToModel, WithHeadingRow, WithCustomValueBinder
{
    protected $tahunAjaran;
    public $berhasil = 0;
    public $gagal = 0;
    public $tidakDitemukan = [];

    public function __construct($tahunAjaran)
    {
        $this->tahunAjaran = $tahunAjaran;
    }

    public function model(array $row)
    {
        // Ambil NISN
        $nisn = trim((string) ($row['nisn'] ?? ''));
        
        if (empty($nisn)) {
            $this->gagal++;
            return null;
        }

        // Cari siswa
        $siswa = Student::where('nisn', $nisn)->first();

        if (!$siswa) {
            $this->tidakDitemukan[] = $nisn;
            $this->gagal++;
            return null;
        }

        // 🔥 AMBIL NILAI RATA-RATA (coba berbagai nama kolom)
        $nilaiTotal = $row['rata_rata'] 
            ?? $row['nilai_total'] 
            ?? $row['nilai'] 
            ?? $row['rata'] 
            ?? $row['nilai_asat']
            ?? $row['nilai_akhir']
            ?? $row['nilai_rapor']
            ?? $row['total']
            ?? $row['jumlah']
            ?? null;

        // 🔥 KALAU TIDAK ADA KOLOM RATA-RATA, HITUNG DARI MAPEL
        if ($nilaiTotal === null || $nilaiTotal === '') {
            // Ambil semua nilai mapel (semua kolom kecuali nisn, nama, kelas)
            $kolomSkip = ['nisn', 'nama', 'kelas', 'no', 'nis', 'nama_siswa'];
            $nilaiMapel = [];
            
            foreach ($row as $key => $value) {
                if (in_array(strtolower($key), $kolomSkip)) continue;
                if (is_numeric($value) && $value >= 0 && $value <= 100) {
                    $nilaiMapel[] = (float) $value;
                }
            }
            
            if (empty($nilaiMapel)) {
                $this->gagal++;
                return null;
            }
            
            $nilaiTotal = array_sum($nilaiMapel) / count($nilaiMapel);
        }

        // Normalisasi nilai
        if (is_string($nilaiTotal)) {
            $nilaiTotal = str_replace(',', '.', $nilaiTotal);
        }

        // Simpan/update
        NilaiAsat::updateOrCreate(
            [
                'student_id' => $siswa->id,
                'tahun_ajaran' => $this->tahunAjaran,
            ],
            [
                'kelas' => $siswa->class,
                'nilai_total' => (float) $nilaiTotal,
            ]
        );

        $this->berhasil++;
        return null;
    }

    public function bindValue(Cell $cell, $value)
    {
        $column = $cell->getColumn();
        if ($column === 'A') {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }
        return parent::bindValue($cell, $value);
    }
}