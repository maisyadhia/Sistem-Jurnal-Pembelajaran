<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Exports\GuruExport;
use App\Exports\SiswaExport;
use App\Exports\KelasExport;
use App\Exports\MapelExport;
use App\Exports\JadwalExport;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class DataMasterController extends Controller
{
    use LogsAdminActivity;

    public function index()
    {
        $totalGuru   = DB::table('guru')->count();
        $totalSiswa  = DB::table('students')->count();
        $totalKelas  = DB::table('kelas_master')->count();
        $totalMapel  = DB::table('mapel_master')->count();
        $totalJadwal = DB::table('jadwals')->count();

        return view('admin.data-master.index', compact(
            'totalGuru',
            'totalSiswa',
            'totalKelas',
            'totalMapel',
            'totalJadwal'
        ));
    }

    // ====== 1. GURU ======
    public function importGuru(Request $request)
    {
        $request->validate([
            'file' => 'required|max:5120'
        ], [
            'file.required' => 'File CSV atau XLSX wajib diunggah!',
            'file.max'      => 'Ukuran file tidak boleh lebih dari 5MB.'
        ]);

        try {
            $file = $request->file('file');
            if (!$file || !$file->isValid()) {
                session()->flash('error', 'File unggahan tidak valid atau corrupt.');
                return redirect()->route('data-master.guru');
            }

            $extension = strtolower($file->getClientOriginalExtension());
            $rows = $extension === 'xlsx' ? $this->parseXlsxNative($file->getRealPath()) : $this->parseCsvNative($file->getRealPath());

            if (empty($rows)) {
                session()->flash('error', 'File kosong atau format file tidak didukung.');
                return redirect()->route('data-master.guru');
            }

            $kolomTabel = DB::getSchemaBuilder()->getColumnListing('guru');
            $importedCount = 0;

            foreach ($rows as $row) {
                $row = array_map(function($val) {
                    return is_string($val) ? trim($val) : $val;
                }, $row);

                if (empty(array_filter($row))) {
                    continue;
                }

                $kodeGuru = $row[0] ?? '';
                $nikRaw   = $row[1] ?? null;
                $namaGuru = $row[2] ?? '';
                $passRaw  = $row[3] ?? null;
                $role     = $row[4] ?? 'guru';

                $firstColLower = strtolower(trim((string)$kodeGuru));
                if (in_array($firstColLower, ['kode_guru', 'kode', 'no', 'nomor', 'nik', 'nama_guru', 'nama'])) {
                    continue;
                }

                if (empty($namaGuru) || empty($kodeGuru)) {
                    continue;
                }

                $cleanNik = !empty($nikRaw) ? trim(str_replace(["'", " ", "\xc2\xa0"], '', (string)$nikRaw)) : null;
                if ($cleanNik === '-') $cleanNik = null;

                $existing = DB::table('guru')
                    ->when($cleanNik, function ($q) use ($cleanNik) {
                        $q->where('nik', $cleanNik);
                    })
                    ->orWhere('nama_guru', $namaGuru)
                    ->first();

                $dataPayload = [];
                if (in_array('kode_guru', $kolomTabel)) $dataPayload['kode_guru'] = $kodeGuru;
                if (in_array('nama_guru', $kolomTabel)) $dataPayload['nama_guru'] = $namaGuru;
                if (in_array('nik', $kolomTabel)) $dataPayload['nik'] = $cleanNik;
                if (in_array('nip', $kolomTabel) && !isset($dataPayload['nik'])) $dataPayload['nip'] = $cleanNik;
                if (in_array('role', $kolomTabel)) $dataPayload['role'] = strtolower($role) ?: 'guru';
                if (in_array('updated_at', $kolomTabel)) $dataPayload['updated_at'] = now();

                if ($existing) {
                    if (!empty($passRaw) && in_array('password', $kolomTabel)) {
                        $dataPayload['password'] = bcrypt($passRaw);
                    }
                    DB::table('guru')->where('id', $existing->id)->update($dataPayload);
                } else {
                    if (in_array('password', $kolomTabel)) {
                        $dataPayload['password'] = bcrypt($passRaw ?: ($cleanNik ?? '12345678'));
                    }
                    if (in_array('created_at', $kolomTabel)) {
                        $dataPayload['created_at'] = now();
                    }
                    DB::table('guru')->insert($dataPayload);
                }

                $importedCount++;
            }

            if ($importedCount === 0) {
                session()->flash('warning', 'Tidak ada data baru yang diimpor. Pastikan format file sesuai.');
                return redirect()->route('data-master.guru');
            }
            
            if (method_exists($this, 'logActivity')) {
                $this->logActivity('import', 'guru', "Import {$importedCount} data guru (Format: {$extension})");
            }

            return back()->with('success', "Berhasil mengimpor {$importedCount} data guru ke database!");

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal impor data guru: ' . $e->getMessage());
        }
    }

   // ====== 2. SISWA ======
    public function importSiswa(Request $request)
    {
        $request->validate([
            'file' => 'required|max:5120'
        ], [
            'file.required' => 'File CSV atau XLSX wajib diunggah!',
            'file.max'      => 'Ukuran file tidak boleh lebih dari 5MB.'
        ]);

        try {
            $file = $request->file('file');
            if (!$file || !$file->isValid()) {
                return back()->with('error', 'File unggahan tidak valid atau corrupt.');
            }

            $extension = strtolower($file->getClientOriginalExtension());
            $rows = $extension === 'xlsx' ? $this->parseXlsxNative($file->getRealPath()) : $this->parseCsvNative($file->getRealPath());

            if (empty($rows)) {
                return back()->with('error', 'File kosong atau format file tidak didukung.');
            }

            $importedCount = 0;
            foreach ($rows as $row) {
                $row = array_map(function($val) {
                    return is_string($val) ? trim($val) : $val;
                }, $row);

                if (empty(array_filter($row))) continue;

                // Urutan kolom: [0] => nisn, [1] => tanggal_lahir, [2] => name, [3] => class, [4] => parent_name, [5] => parent_phone
                $nisnRaw    = $row[0] ?? null;
                $tglRaw     = $row[1] ?? null;
                $name       = $row[2] ?? '';
                $class      = $row[3] ?? null;
                $parentName = $row[4] ?? null;
                $parentPhone= $row[5] ?? null;

                $firstColLower = strtolower(trim((string)$nisnRaw));
                if (in_array($firstColLower, ['nisn', 'no', 'nomor', 'nama', 'nama_siswa', 'name'])) {
                    continue;
                }

                if (empty($name)) continue;

                $cleanNisn = !empty($nisnRaw) ? trim(str_replace(["'", " ", "\xc2\xa0"], '', (string)$nisnRaw)) : null;

                // Konversi format tanggal Excel serial (jika berupa angka seperti 38706) menjadi tanggal Y-m-d
                $tglLahir = null;
                if (!empty($tglRaw)) {
                    if (is_numeric($tglRaw)) {
                        $tglLahir = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($tglRaw)->format('Y-m-d');
                    } else {
                        $parsedDate = date_create($tglRaw);
                        $tglLahir = $parsedDate ? date_format($parsedDate, 'Y-m-d') : null;
                    }
                }

                $existing = DB::table('students')
                    ->when($cleanNisn, function($q) use ($cleanNisn) {
                        $q->where('nisn', $cleanNisn);
                    })
                    ->orWhere('name', $name)
                    ->first();

                $payload = [
                    'name'        => $name,
                    'nisn'        => $cleanNisn,
                    'class'       => $class,
                    'parent_name' => $parentName,
                    'updated_at'  => now(),
                ];

                $columns = DB::getSchemaBuilder()->getColumnListing('students');
                if (in_array('dob', $columns) || in_array('tanggal_lahir', $columns)) {
                    $payload[in_array('dob', $columns) ? 'dob' : 'tanggal_lahir'] = $tglLahir;
                }
                if (in_array('parent_phone', $columns) || in_array('no_telepon_orang_tua', $columns)) {
                    $payload[in_array('parent_phone', $columns) ? 'parent_phone' : 'no_telepon_orang_tua'] = $parentPhone;
                }

                if ($existing) {
                    DB::table('students')->where('id', $existing->id)->update($payload);
                } else {
                    $payload['created_at'] = now();
                    DB::table('students')->insert($payload);
                }

                $importedCount++;
            }

            if ($importedCount === 0) {
                return back()->with('warning', 'Tidak ada data siswa baru yang diimpor.');
            }

            if (method_exists($this, 'logActivity')) {
                $this->logActivity('import', 'siswa', "Import {$importedCount} data siswa");
            }

            return back()->with('success', "Berhasil mengimpor {$importedCount} data siswa ke database!");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimpor data siswa: ' . $e->getMessage());
        }
    }
    
    // ====== 3. KELAS ======
    public function importKelas(Request $request)
    {
        $request->validate([
            'file' => 'required|max:5120'
        ]);

        try {
            $file = $request->file('file');
            if (!$file || !$file->isValid()) {
                return back()->with('error', 'File unggahan tidak valid atau corrupt.');
            }

            $extension = strtolower($file->getClientOriginalExtension());
            $rows = $extension === 'xlsx' ? $this->parseXlsxNative($file->getRealPath()) : $this->parseCsvNative($file->getRealPath());

            if (empty($rows)) {
                return back()->with('error', 'File kosong atau format file tidak didukung.');
            }

            $importedCount = 0;
            foreach ($rows as $row) {
                $row = array_map(function($val) {
                    return is_string($val) ? trim($val) : $val;
                }, $row);

                if (empty(array_filter($row))) continue;

                // Urutan kolom: [0] => nama_kelas, [1] => wali_kelas
                $namaKelas  = $row[0] ?? '';
                $waliKelas  = $row[1] ?? null;

                $firstColLower = strtolower(trim((string)$namaKelas));
                if (in_array($firstColLower, ['nama_kelas', 'kelas', 'no', 'nomor'])) {
                    continue;
                }

                if (empty($namaKelas)) continue;

                $existing = DB::table('kelas_master')->where('nama_kelas', $namaKelas)->first();

                $payload = [
                    'nama_kelas' => $namaKelas,
                    'wali_kelas' => $waliKelas,
                    'updated_at' => now(),
                ];

                if ($existing) {
                    DB::table('kelas_master')->where('id', $existing->id)->update($payload);
                } else {
                    $payload['created_at'] = now();
                    DB::table('kelas_master')->insert($payload);
                }

                $importedCount++;
            }

            if ($importedCount === 0) {
                return back()->with('warning', 'Tidak ada data kelas baru yang diimpor.');
            }

            if (method_exists($this, 'logActivity')) {
                $this->logActivity('import', 'kelas', "Import {$importedCount} data kelas");
            }

            return back()->with('success', "Berhasil mengimpor {$importedCount} data kelas ke database!");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimpor data kelas: ' . $e->getMessage());
        }
    }

    // ====== 4. MAPEL ======
    public function importMapel(Request $request)
    {
        $request->validate([
            'file' => 'required|max:5120'
        ]);

        try {
            $file = $request->file('file');
            if (!$file || !$file->isValid()) {
                return back()->with('error', 'File unggahan tidak valid atau corrupt.');
            }

            $extension = strtolower($file->getClientOriginalExtension());
            $rows = $extension === 'xlsx' ? $this->parseXlsxNative($file->getRealPath()) : $this->parseCsvNative($file->getRealPath());

            if (empty($rows)) {
                return back()->with('error', 'File kosong atau format file tidak didukung.');
            }

            $importedCount = 0;
            foreach ($rows as $row) {
                $row = array_map(function($val) {
                    return is_string($val) ? trim($val) : $val;
                }, $row);

                if (empty(array_filter($row))) continue;

                // Urutan kolom: [0] => kode_mapel, [1] => nama_mapel
                $kodeMapel = $row[0] ?? '';
                $namaMapel = $row[1] ?? '';

                $firstColLower = strtolower(trim((string)$kodeMapel));
                if (in_array($firstColLower, ['kode_mapel', 'kode', 'no', 'nomor', 'nama_mapel'])) {
                    continue;
                }

                if (empty($namaMapel)) {
                    if (!empty($kodeMapel)) {
                        $namaMapel = $kodeMapel;
                        $kodeMapel = null;
                    } else {
                        continue;
                    }
                }

                $existing = DB::table('mapel_master')->where('nama_mapel', $namaMapel)->first();

                $payload = [
                    'nama_mapel' => $namaMapel,
                    'kode_mapel' => $kodeMapel,
                    'updated_at' => now(),
                ];

                if ($existing) {
                    DB::table('mapel_master')->where('id', $existing->id)->update($payload);
                } else {
                    $payload['created_at'] = now();
                    DB::table('mapel_master')->insert($payload);
                }

                $importedCount++;
            }

            if ($importedCount === 0) {
                return back()->with('warning', 'Tidak ada data mata pelajaran baru yang diimpor.');
            }

            if (method_exists($this, 'logActivity')) {
                $this->logActivity('import', 'mapel', "Import {$importedCount} data mapel");
            }

            return back()->with('success', 'Berhasil mengimpor data mata pelajaran ke database!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimpor data mata pelajaran: ' . $e->getMessage());
        }
    }

// ====== 5. JADWAL ======
    public function importJadwal(Request $request)
    {
        $request->validate([
            'file' => 'required|max:5120'
        ]);

        try {
            $file = $request->file('file');
            if (!$file || !$file->isValid()) {
                return back()->with('error', 'File unggahan tidak valid atau corrupt.');
            }

            $extension = strtolower($file->getClientOriginalExtension());
            $rows = $extension === 'xlsx' ? $this->parseXlsxNative($file->getRealPath()) : $this->parseCsvNative($file->getRealPath());

            if (empty($rows)) {
                return back()->with('error', 'File kosong atau format file tidak didukung.');
            }

            $importedCount = 0;
            foreach ($rows as $row) {
                $row = array_map(function($val) {
                    return is_string($val) ? trim($val) : $val;
                }, $row);

                if (empty(array_filter($row))) continue;

                // Urutan kolom: [0] => hari, [1] => jam_ke, [2] => nama_guru, [3] => nama_kelas, [4] => nama_mapel, [5] => jam_mulai, [6] => jam_selesai
                $hari       = $row[0] ?? '';
                $jamKe      = $row[1] ?? '';
                $namaGuru   = $row[2] ?? null;
                $namaKelas  = $row[3] ?? '';
                $namaMapel  = $row[4] ?? '';
                $rawMulai   = $row[5] ?? null;
                $rawSelesai = $row[6] ?? null;

                $firstColLower = strtolower(trim((string)$hari));
                if (in_array($firstColLower, ['hari', 'day', 'no', 'nomor'])) {
                    continue;
                }

                if (empty($hari) || empty($namaKelas) || empty($namaMapel)) {
                    continue;
                }

                // Handler konversi jam mulai & selesai agar terbaca sempurna (mendukung format waktu Excel / teks)
                $jamMulai = null;
                if (!empty($rawMulai)) {
                    if (is_numeric($rawMulai)) {
                        $jamMulai = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rawMulai)->format('H:i:s');
                    } else {
                        $parsedMulai = date_create($rawMulai);
                        $jamMulai = $parsedMulai ? date_format($parsedMulai, 'H:i:s') : $rawMulai;
                    }
                }

                $jamSelesai = null;
                if (!empty($rawSelesai)) {
                    if (is_numeric($rawSelesai)) {
                        $jamSelesai = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($rawSelesai)->format('H:i:s');
                    } else {
                        $parsedSelesai = date_create($rawSelesai);
                        $jamSelesai = $parsedSelesai ? date_format($parsedSelesai, 'H:i:s') : $rawSelesai;
                    }
                }

                $guruRec  = !empty($namaGuru) ? DB::table('guru')->where('nama_guru', 'LIKE', "%{$namaGuru}%")->first() : null;
                $kelasRec = DB::table('kelas_master')->where('nama_kelas', $namaKelas)->first();
                $mapelRec = DB::table('mapel_master')->where('nama_mapel', $namaMapel)->first();

                if (!$kelasRec || !$mapelRec) {
                    continue;
                }

                $existing = DB::table('jadwals')
                    ->where('hari', $hari)
                    ->where('jam_ke', $jamKe)
                    ->where('kelas_id', $kelasRec->id)
                    ->where('mapel_id', $mapelRec->id)
                    ->first();

                $payload = [
                    'hari'        => $hari,
                    'jam_ke'      => $jamKe,
                    'guru_id'     => $guruRec ? $guruRec->id : null,
                    'kelas_id'    => $kelasRec->id,
                    'mapel_id'    => $mapelRec->id,
                    'jam_mulai'   => $jamMulai,
                    'jam_selesai' => $jamSelesai,
                    'updated_at'  => now(),
                ];

                if ($existing) {
                    DB::table('jadwals')->where('id', $existing->id)->update($payload);
                } else {
                    $payload['created_at'] = now();
                    DB::table('jadwals')->insert($payload);
                }

                $importedCount++;
            }

            if ($importedCount === 0) {
                return back()->with('warning', 'Tidak ada data jadwal baru yang diimpor. Pastikan nama kelas dan mapel sudah terdaftar.');
            }

            if (method_exists($this, 'logActivity')) {
                $this->logActivity('import', 'jadwal', "Import {$importedCount} data jadwal");
            }

            return back()->with('success', 'Berhasil mengimpor data jadwal mengajar ke database!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengimpor data jadwal: ' . $e->getMessage());
        }
    }

    /**
     * Helper Parser CSV Native PHP
     */
    private function parseCsvNative($filePath)
    {
        $rows = [];
        $handle = fopen($filePath, 'r');
        if ($handle !== false) {
            $header = fgetcsv($handle, 2000, ',');
            $separator = ',';
            if ($header && count($header) === 1 && strpos($header[0], ';') !== false) {
                rewind($handle);
                $separator = ';';
            } else {
                rewind($handle);
            }

            while (($data = fgetcsv($handle, 2000, $separator)) !== false) {
                $rows[] = array_values($data);
            }
            fclose($handle);
        }
        return $rows;
    }

    /**
     * Helper Parser XLSX Native PHP
     */
    private function parseXlsxNative($filePath)
    {
        $rows = [];
        $zip = new \ZipArchive;

        if ($zip->open($filePath) === true) {
            $strings = [];
            if (($sharedIndex = $zip->locateName('xl/sharedStrings.xml')) !== false) {
                $xmlStrings = simplexml_load_string($zip->getFromIndex($sharedIndex));
                foreach ($xmlStrings->si as $val) {
                    if (isset($val->t)) {
                        $strings[] = (string)$val->t;
                    } elseif (isset($val->r)) {
                        $text = '';
                        foreach ($val->r as $run) {
                            $text .= (string)$run->t;
                        }
                        $strings[] = $text;
                    } else {
                        $strings[] = '';
                    }
                }
            }

            if (($sheetIndex = $zip->locateName('xl/worksheets/sheet1.xml')) !== false) {
                $xmlSheet = simplexml_load_string($zip->getFromIndex($sheetIndex));
                foreach ($xmlSheet->sheetData->row as $r) {
                    $rowItems = [];
                    foreach ($r->c as $c) {
                        $cellRef = (string)$c['r'];
                        preg_match('/([A-Z]+)/', $cellRef, $matches);
                        $colLetters = $matches[1] ?? 'A';
                        
                        $colIdx = 0;
                        for ($i = 0; $i < strlen($colLetters); $i++) {
                            $colIdx = $colIdx * 26 + (ord($colLetters[$i]) - 64);
                        }
                        $colIdx -= 1;

                        $type = (string)($c['t'] ?? '');
                        $val  = (string)$c->v;

                        if ($type === 's' && isset($strings[(int)$val])) {
                            $val = $strings[(int)$val];
                        }

                        $rowItems[$colIdx] = $val;
                    }

                    if (!empty($rowItems)) {
                        ksort($rowItems);
                        $maxKey = max(array_keys($rowItems));
                        $normalizedRow = [];
                        for ($k = 0; $k <= $maxKey; $k++) {
                            $normalizedRow[$k] = $rowItems[$k] ?? '';
                        }
                        $rows[] = $normalizedRow;
                    }
                }
            }
            $zip->close();
        }

        return $rows;
    }

    // ====== EXPORT GURU ======
    public function exportGuru()
    {
        date_default_timezone_set('Asia/Jakarta');

        try {
            $dataGuru = DB::table('guru')->orderBy('nama_guru', 'asc')->get();

            if ($dataGuru->isEmpty()) {
                return back()->with('warning', 'Data guru kosong, tidak ada yang diunduh.');
            }

            if (method_exists($this, 'logActivity')) {
                $this->logActivity('export', 'guru', 'Export data guru ke Excel (CSV)');
            }

            $fileName = 'Data_Guru_MIN2_Malang_' . date('Y-m-d_H-i') . '.csv';

            $headers = [
                "Content-type"        => "text/csv; charset=UTF-8",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $callback = function () use ($dataGuru) {
                $file = fopen('php://output', 'w');
                fputs($file, "\xEF\xBB\xBF");

                fputcsv($file, ['kode_guru', 'nik', 'nama_guru', 'password', 'role']);

                foreach ($dataGuru as $g) {
                    $nikNip = !empty($g->nik) ? "'" . $g->nik : (!empty($g->nip) ? "'" . $g->nip : '');

                    fputcsv($file, [
                        $g->kode_guru ?? '',
                        $nikNip,
                        $g->nama_guru ?? '',
                        '',
                        $g->role ?? 'guru'
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);

        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengunduh data guru: ' . $e->getMessage());
        }
    }

    // ====== EXPORT SISWA ======
    public function exportSiswa()
    {
        date_default_timezone_set('Asia/Jakarta');

        try {
            $dataSiswa = DB::table('students')->orderBy('name', 'asc')->get();

            if ($dataSiswa->isEmpty()) {
                return back()->with('warning', 'Data siswa kosong, tidak ada yang diunduh.');
            }

            if (method_exists($this, 'logActivity')) {
                $this->logActivity('export', 'siswa', 'Export data siswa ke CSV');
            }

            $fileName = 'Data_Siswa_MIN2_Malang_' . date('Y-m-d_H-i') . '.csv';

            $headers = [
                "Content-type"        => "text/csv; charset=UTF-8",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $callback = function () use ($dataSiswa) {
                $file = fopen('php://output', 'w');
                fputs($file, "\xEF\xBB\xBF");

                fputcsv($file, ['nisn', 'tanggal_lahir', 'nama_siswa', 'kelas', 'nama_orang_tua', 'no_telepon_orang_tua']);

                foreach ($dataSiswa as $s) {
                    $nisnClean = !empty($s->nisn) ? "'" . $s->nisn : '';
                    fputcsv($file, [
                        $nisnClean,
                        $s->dob ?? ($s->tanggal_lahir ?? ''),
                        $s->name ?? '',
                        $s->class ?? '',
                        $s->parent_name ?? '',
                        $s->parent_phone ?? ($s->no_telepon_orang_tua ?? '')
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengunduh data siswa: ' . $e->getMessage());
        }
    }

    // ====== EXPORT KELAS ======
    public function exportKelas()
    {
        date_default_timezone_set('Asia/Jakarta');

        try {
            $dataKelas = DB::table('kelas_master')->orderBy('nama_kelas', 'asc')->get();

            if ($dataKelas->isEmpty()) {
                return back()->with('warning', 'Data kelas kosong, tidak ada yang diunduh.');
            }

            if (method_exists($this, 'logActivity')) {
                $this->logActivity('export', 'kelas', 'Export data kelas ke CSV');
            }

            $fileName = 'Data_Kelas_MIN2_Malang_' . date('Y-m-d_H-i') . '.csv';

            $headers = [
                "Content-type"        => "text/csv; charset=UTF-8",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $callback = function () use ($dataKelas) {
                $file = fopen('php://output', 'w');
                fputs($file, "\xEF\xBB\xBF");

                fputcsv($file, ['nama_kelas', 'wali_kelas']);

                foreach ($dataKelas as $k) {
                    fputcsv($file, [
                        $k->nama_kelas ?? '',
                        $k->wali_kelas ?? ''
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengunduh data kelas: ' . $e->getMessage());
        }
    }

    // ====== EXPORT MAPEL ======
    public function exportMapel()
    {
        date_default_timezone_set('Asia/Jakarta');

        try {
            $dataMapel = DB::table('mapel_master')->orderBy('nama_mapel', 'asc')->get();

            if ($dataMapel->isEmpty()) {
                return back()->with('warning', 'Data mapel kosong, tidak ada yang diunduh.');
            }

            if (method_exists($this, 'logActivity')) {
                $this->logActivity('export', 'mapel', 'Export data mapel ke CSV');
            }

            $fileName = 'Data_Mapel_MIN2_Malang_' . date('Y-m-d_H-i') . '.csv';

            $headers = [
                "Content-type"        => "text/csv; charset=UTF-8",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $callback = function () use ($dataMapel) {
                $file = fopen('php://output', 'w');
                fputs($file, "\xEF\xBB\xBF");

                fputcsv($file, ['kode_mapel', 'nama_mapel']);

                foreach ($dataMapel as $m) {
                    fputcsv($file, [
                        $m->kode_mapel ?? '',
                        $m->nama_mapel ?? ''
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengunduh data mata pelajaran: ' . $e->getMessage());
        }
    }

    // ====== EXPORT JADWAL ======
    public function exportJadwal()
    {
        date_default_timezone_set('Asia/Jakarta');

        try {
            $dataJadwal = DB::table('jadwals')
                ->leftJoin('guru', 'jadwals.guru_id', '=', 'guru.id')
                ->leftJoin('kelas_master', 'jadwals.kelas_id', '=', 'kelas_master.id')
                ->leftJoin('mapel_master', 'jadwals.mapel_id', '=', 'mapel_master.id')
                ->select('jadwals.*', 'guru.nama_guru', 'kelas_master.nama_kelas', 'mapel_master.nama_mapel')
                ->orderBy('jadwals.hari', 'asc')
                ->get();

            if ($dataJadwal->isEmpty()) {
                return back()->with('warning', 'Data jadwal kosong, tidak ada yang diunduh.');
            }

            if (method_exists($this, 'logActivity')) {
                $this->logActivity('export', 'jadwal', 'Export data jadwal ke CSV');
            }

            $fileName = 'Data_Jadwal_MIN2_Malang_' . date('Y-m-d_H-i') . '.csv';

            $headers = [
                "Content-type"        => "text/csv; charset=UTF-8",
                "Content-Disposition" => "attachment; filename=$fileName",
                "Pragma"              => "no-cache",
                "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
                "Expires"             => "0"
            ];

            $callback = function () use ($dataJadwal) {
                $file = fopen('php://output', 'w');
                fputs($file, "\xEF\xBB\xBF");

                fputcsv($file, ['hari', 'jam_ke', 'nama_guru', 'nama_kelas', 'nama_mapel', 'jam_mulai', 'jam_selesai']);

                foreach ($dataJadwal as $j) {
                    fputcsv($file, [
                        $j->hari ?? '',
                        $j->jam_ke ?? '',
                        $j->nama_guru ?? '',
                        $j->nama_kelas ?? '',
                        $j->nama_mapel ?? '',
                        $j->jam_mulai ?? '',
                        $j->jam_selesai ?? ''
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal mengunduh data jadwal: ' . $e->getMessage());
        }
    }
}