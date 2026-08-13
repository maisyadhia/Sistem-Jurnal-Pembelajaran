<?php

namespace App\Imports;

use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class SiswaImport extends DefaultValueBinder implements 
    ToModel, 
    WithHeadingRow, 
    WithValidation,
    WithCustomValueBinder
{
    public function model(array $row)
    {
        // Bersihkan NISN
        $nisn = trim((string) $row['nisn']);
        
        // Cek duplikasi
        $exists = DB::table('students')
            ->where('nisn', $nisn)
            ->exists();

        if ($exists) {
            return null;
        }

        // 🔥 Proses tanggal lahir - support berbagai format
        $dob = $this->parseDate($row['tanggal_lahir'] ?? null);
        
        if (!$dob) {
            return null; // Skip jika tanggal tidak valid
        }

        return new Student([
            'nisn' => $nisn,
            'name' => $row['nama'] ?? '',
            'dob' => $dob,
            'class' => $row['kelas'] ?? '',
            'parent_name' => $row['orang_tua'] ?? null,
            'parent_phone' => $row['no_telepon'] ?? null,
        ]);
    }

    private function parseDate($date)
    {
        if (empty($date)) {
            return null;
        }

        // Jika tanggal dalam format Excel serial number
        if (is_numeric($date)) {
            return Carbon::createFromFormat('Y-m-d', '1899-12-30')
                ->addDays($date - 1)
                ->format('Y-m-d');
        }

        // Jika tanggal dalam format dd/mm/yy atau dd/mm/yyyy
        if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{2,4}$/', $date)) {
            $parts = explode('/', $date);
            $day = str_pad($parts[0], 2, '0', STR_PAD_LEFT);
            $month = str_pad($parts[1], 2, '0', STR_PAD_LEFT);
            $year = $parts[2];
            
            // Jika tahun 2 digit, konversi ke 4 digit
            if (strlen($year) == 2) {
                $year = ($year >= 30) ? '19' . $year : '20' . $year;
            }
            
            return $year . '-' . $month . '-' . $day;
        }

        // Jika format YYYY-MM-DD
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
            return $date;
        }

        // Jika format d/m/yy dengan tahun pendek
        if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{2}$/', $date)) {
            $parts = explode('/', $date);
            $day = str_pad($parts[0], 2, '0', STR_PAD_LEFT);
            $month = str_pad($parts[1], 2, '0', STR_PAD_LEFT);
            $year = '20' . $parts[2];
            return $year . '-' . $month . '-' . $day;
        }

        // Coba parse dengan Carbon
        try {
            return Carbon::parse($date)->format('Y-m-d');
        } catch (\Exception $e) {
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'nisn' => 'required',
            'nama' => 'required',
            'kelas' => 'required',
        ];
    }

    public function bindValue(Cell $cell, $value)
    {
        // Pastikan NISN dibaca sebagai teks
        $column = $cell->getColumn();
        if ($column === 'A') { // Kolom NISN
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }
        return parent::bindValue($cell, $value);
    }
}