<?php

namespace App\Imports;

use App\Models\Guru;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class GuruImport extends DefaultValueBinder implements 
    ToModel, 
    WithHeadingRow, 
    WithValidation,
    WithCustomValueBinder
{
    public function model(array $row)
    {
        // Bersihkan NIK dari spasi atau karakter aneh
        $nik = trim((string) $row['nik']);
        // Jika NIK dalam format scientific, konversi
        if (strpos($nik, 'E+') !== false) {
            $nik = number_format((float) $nik, 0, '.', '');
        }

        // Cek duplikasi
        $exists = DB::table('guru')
            ->where('nik', $nik)
            ->orWhere('kode_guru', $row['kode_guru'])
            ->exists();

        if ($exists) {
            return null; // Skip jika sudah ada
        }

        // 🔥 KEMBALIKAN MODEL Guru, BUKAN ARRAY
        return new Guru([
            'kode_guru' => $row['kode_guru'],
            'nik' => $nik,
            'nama_guru' => $row['nama_guru'],
            'password' => Hash::make($row['password'] ?? 'guru123'),
            'role' => $row['role'] ?? 'guru',
        ]);
    }

    public function rules(): array
    {
        return [
            'kode_guru' => 'required|unique:guru,kode_guru',
            'nik' => 'required|min:16',
            'nama_guru' => 'required',
        ];
    }

    public function bindValue(Cell $cell, $value)
    {
        // Pastikan NIK dibaca sebagai teks
        $column = $cell->getColumn();
        if ($column === 'B') {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }
        return parent::bindValue($cell, $value);
    }
}