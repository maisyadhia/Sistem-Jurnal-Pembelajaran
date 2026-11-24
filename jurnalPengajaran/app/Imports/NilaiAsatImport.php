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
        // 1. Ambil NISN & pastikan dibaca sebagai string
        $nisn = isset($row['nisn']) ? trim((string) $row['nisn']) : '';
        
        if (empty($nisn)) {
            $this->gagal++;
            return null;
        }

        // 2. Cari siswa berdasarkan NISN
        $siswa = Student::where('nisn', $nisn)->first();

        if (!$siswa) {
            $this->tidakDitemukan[] = $nisn;
            $this->gagal++;
            return null;
        }

        // 3. Ambil Nilai Total (Coba berbagai kemungkinan header dari RDM)
        $nilaiTotal = $row['rata_rata'] 
            ?? $row['nilai_total'] 
            ?? $row['rata'] 
            ?? $row['jumlah']
            ?? null;

        // 4. Ambil Nilai Per Mapel (Jika ada di file Excel RDM)
        // Sesuaikan nama kolom di sini dengan header Excel dari RDM Anda
        $nilaiBind = $row['bind'] ?? $row['b_indonesia'] ?? $row['bahasa_indonesia'] ?? null;
        $nilaiMat  = $row['mat'] ?? $row['matematika'] ?? null;
        $nilaiIpa  = $row['ipa'] ?? $row['sains'] ?? null;
        $catatan   = $row['catatan'] ?? null;

        // 5. Jika Nilai Total Kosong, Hitung dari Nilai Mapel
        if ($nilaiTotal === null || $nilaiTotal === '') {
            $nilaiMapel = array_filter([$nilaiBind, $nilaiMat, $nilaiIpa], function($val) {
                return is_numeric($val);
            });
            
            if (!empty($nilaiMapel)) {
                $nilaiTotal = array_sum($nilaiMapel) / count($nilaiMapel);
            } else {
                // Jika benar-benar tidak ada nilai, gagalkan baris ini
                $this->gagal++;
                return null;
            }
        }

        // 6. Fungsi bantuan untuk normalisasi format desimal (koma ke titik)
        $formatAngka = function($val) {
            if ($val === null || $val === '') return null;
            return (float) str_replace(',', '.', $val);
        };

        // 7. Simpan ke Database
        NilaiAsat::updateOrCreate(
            [
                'student_id' => $siswa->id,
                'tahun_ajaran' => $this->tahunAjaran,
            ],
            [
                'kelas' => $siswa->class, // Ambil kelas dari data siswa
                'nilai_total' => $formatAngka($nilaiTotal),
                'nilai_bind' => $formatAngka($nilaiBind),
                'nilai_mat'  => $formatAngka($nilaiMat),
                'nilai_ipa'  => $formatAngka($nilaiIpa),
                'catatan'    => $catatan,
            ]
        );

        $this->berhasil++;
        return null;
    }

    public function bindValue(Cell $cell, $value)
    {
        // Memaksa kolom A dan B dibaca sebagai teks (NISN sering jadi angka di Excel)
        $column = $cell->getColumn();
        if ($column === 'A' || $column === 'B') {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }
        return parent::bindValue($cell, $value);
    }
}