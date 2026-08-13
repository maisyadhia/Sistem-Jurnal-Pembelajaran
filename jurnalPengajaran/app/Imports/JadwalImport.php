<?php

namespace App\Imports;

use App\Models\Jadwal;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class JadwalImport extends DefaultValueBinder implements 
    ToModel, 
    WithHeadingRow, 
    WithValidation,
    WithCustomValueBinder
{
    // Mapping jam ke waktu
    private $jamMapping = [
        0 => ['mulai' => '06:30:00', 'selesai' => '07:00:00'],
        1 => ['mulai' => '07:00:00', 'selesai' => '07:35:00'],
        2 => ['mulai' => '07:35:00', 'selesai' => '08:10:00'],
        3 => ['mulai' => '08:10:00', 'selesai' => '08:45:00'],
        4 => ['mulai' => '08:45:00', 'selesai' => '09:20:00'],
        5 => ['mulai' => '09:35:00', 'selesai' => '10:10:00'],
        6 => ['mulai' => '10:10:00', 'selesai' => '10:45:00'],
        7 => ['mulai' => '10:45:00', 'selesai' => '11:20:00'],
        8 => ['mulai' => '11:35:00', 'selesai' => '12:10:00'],
        9 => ['mulai' => '12:45:00', 'selesai' => '13:20:00'],
        10 => ['mulai' => '13:20:00', 'selesai' => '13:55:00'],
    ];

    public function model(array $row)
    {
        // Cari ID dari kode
        $guru = DB::table('guru')->where('kode_guru', $row['kode_guru'] ?? '')->first();
        $kelas = DB::table('kelas_master')->where('nama_kelas', $row['kelas'] ?? '')->first();
        $mapel = DB::table('mapel_master')->where('kode_mapel', $row['kode_mapel'] ?? '')->first();

        if (!$guru || !$kelas || !$mapel) {
            return null;
        }

        // Ambil jam_ke
        $jamKe = (int) ($row['jam_ke'] ?? 0);
        
        // 🔥 AMBIL WAKTU DARI MAPPING
        $waktu = $this->jamMapping[$jamKe] ?? ['mulai' => '00:00:00', 'selesai' => '00:00:00'];

        // 🔥 FORMAT WAKTU DARI EXCEL - support berbagai format
        $jamMulai = $this->formatTime($row['jam_mulai'] ?? null, $waktu['mulai']);
        $jamSelesai = $this->formatTime($row['jam_selesai'] ?? null, $waktu['selesai']);

        // Cek duplikasi
        $exists = DB::table('jadwals')
            ->where('guru_id', $guru->id)
            ->where('kelas_id', $kelas->id)
            ->where('mapel_id', $mapel->id)
            ->where('hari', $row['hari'] ?? '')
            ->where('jam_ke', $jamKe)
            ->exists();

        if ($exists) {
            return null;
        }

        return new Jadwal([
            'guru_id' => $guru->id,
            'kelas_id' => $kelas->id,
            'mapel_id' => $mapel->id,
            'hari' => $row['hari'] ?? 'Senin',
            'jam_ke' => $jamKe,
            'jam_mulai' => $jamMulai,
            'jam_selesai' => $jamSelesai,
        ]);
    }

    /**
     * Format waktu dari berbagai format ke HH:MM:SS
     */
    private function formatTime($value, $default = '00:00:00')
    {
        if (empty($value)) {
            return $default;
        }

        // Jika nilai adalah angka (Excel time)
        if (is_numeric($value)) {
            // Konversi Excel time ke format waktu
            $hours = floor($value * 24);
            $minutes = round(($value * 24 - $hours) * 60);
            $seconds = 0;
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }

        // Jika format HH:MM:SS
        if (preg_match('/^(\d{2}):(\d{2}):(\d{2})$/', $value, $matches)) {
            return $value;
        }

        // Jika format HH:MM
        if (preg_match('/^(\d{2}):(\d{2})$/', $value, $matches)) {
            return $matches[1] . ':' . $matches[2] . ':00';
        }

        // Jika format HH.MM.SS
        if (preg_match('/^(\d{2})\.(\d{2})\.(\d{2})$/', $value, $matches)) {
            return $matches[1] . ':' . $matches[2] . ':' . $matches[3];
        }

        // Jika format HH.MM
        if (preg_match('/^(\d{2})\.(\d{2})$/', $value, $matches)) {
            return $matches[1] . ':' . $matches[2] . ':00';
        }

        // Jika format HH:MM:SS AM/PM
        try {
            return \Carbon\Carbon::parse($value)->format('H:i:s');
        } catch (\Exception $e) {
            return $default;
        }
    }

    public function rules(): array
    {
        return [
            'kode_guru' => 'required',
            'kelas' => 'required',
            'kode_mapel' => 'required',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'jam_ke' => 'required|integer|min:0|max:10',
        ];
    }

    public function bindValue(Cell $cell, $value)
    {
        // Pastikan jam_mulai dan jam_selesai dibaca sebagai teks
        $column = $cell->getColumn();
        if ($column === 'F' || $column === 'G') { // Kolom F = jam_mulai, G = jam_selesai
            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }
        return parent::bindValue($cell, $value);
    }
}