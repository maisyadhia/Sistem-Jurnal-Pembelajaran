<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class GuruDashboardController extends Controller
{
    // Halaman Pilih Sesi (route: guru.pilih.sesi)
    public function index()
    {
        $guruId = session('guru_id');

        if (!$guruId) {
            if (session('admin_id')) {
                $guruId = session('admin_id');
            } else {
                return redirect()->route('login');
            }
        }

        // --- AMBIL DATA JADWAL HARI INI BERDASARKAN HARI AKTIF UTK BAHASA INDONESIA ---
        Carbon::setLocale('id'); 
        $namaHariIndo = Carbon::now()->translatedFormat('l'); 

        // Menangkap data kombinasi jadwal khusus hari ini milik guru 
        $jadwalHariIni = DB::table('jadwals')
            ->where('guru_id', $guruId)
            ->where('hari', $namaHariIndo)
            ->select('kelas_id', 'mapel_id')
            ->get();

        // --- DATA PILIHAN DROPDOWN ---
        $daftar_kelas = DB::table('jadwals')
            ->join('kelas_master', 'jadwals.kelas_id', '=', 'kelas_master.id')
            ->where('jadwals.guru_id', $guruId)
            ->select('kelas_master.id as id', 'kelas_master.nama_kelas as nama_kelas')
            ->distinct()
            ->orderBy('kelas_master.nama_kelas')
            ->get();

        $daftar_mapel = DB::table('jadwals')
            ->join('mapel_master', 'jadwals.mapel_id', '=', 'mapel_master.id')
            ->where('jadwals.guru_id', $guruId)
            ->select('mapel_master.id as id', 'mapel_master.nama_mapel as nama_mapel')
            ->distinct()
            ->orderBy('mapel_master.nama_mapel')
            ->get();

        if ($daftar_kelas->isEmpty()) {
            return view('guru.pilih-sesi', compact('daftar_kelas', 'daftar_mapel', 'jadwalHariIni', 'namaHariIndo'))
                ->with('warning', 'Belum ada jadwal yang ditugaskan untuk Anda.');
        }

        return view('guru.pilih-sesi', compact('daftar_kelas', 'daftar_mapel', 'jadwalHariIni', 'namaHariIndo'));
    }

    // Dashboard Ringkasan (route: guru.dashboard)
    public function dashboard(Request $request)
    {
        $guruId = session('guru_id');
        
        if (!$guruId) {
            if (session('admin_id')) {
                $guruId = session('admin_id');
            } else {
                return redirect()->route('login');
            }
        }

        // 💡 1. SYSTEM AUTO-NOTIFIKASI KHUSUS UNTUK ROLE GURU SAJA
        if (session('user_role') === 'guru') {
            Carbon::setLocale('id');
            $today = Carbon::today()->toDateString();
            $namaHariIndo = Carbon::now()->translatedFormat('l');

            $jadwalHariIni = DB::table('jadwals')
                ->join('kelas_master', 'jadwals.kelas_id', '=', 'kelas_master.id')
                ->join('mapel_master', 'jadwals.mapel_id', '=', 'mapel_master.id')
                ->where('jadwals.guru_id', $guruId)
                ->where('jadwals.hari', $namaHariIndo)
                ->select('jadwals.*', 'kelas_master.nama_kelas', 'mapel_master.nama_mapel')
                ->get();

            foreach ($jadwalHariIni as $jadwal) {
                // Cek apakah jurnal sudah diisi hari ini
                $jurnalAda = DB::table('jurnals')
                    ->where('guru_id', $guruId)
                    ->where('kelas_id', $jadwal->kelas_id)
                    ->where('mapel_id', $jadwal->mapel_id)
                    ->whereDate('tanggal', $today)
                    ->exists();

                // 💡 AMBIL KATA UTAMA MAPEL (Pembersihan Spasi & Tanda Kurung)
                $cleanMapel = trim(preg_replace('/\s+/', ' ', str_replace(['(', ')'], '', $jadwal->nama_mapel)));
                $firstMapelWord = explode(' ', $cleanMapel)[0] ?? $jadwal->nama_mapel;

                if ($jurnalAda) {
                    // 🟢 JIKA SUDAH DIISI: Bersihkan HANYA notifikasi kelas & mapel spesifik ini!
                    DB::table('notifications')
                        ->where('user_id', $guruId)
                        ->whereDate('created_at', $today)
                        ->where('is_read', 0)
                        ->where('message', 'like', "%{$jadwal->nama_kelas}%")
                        ->where(function($query) use ($jadwal, $firstMapelWord) {
                            $query->where('message', 'like', "%{$jadwal->nama_mapel}%")
                                  ->orWhere('message', 'like', "%{$firstMapelWord}%");
                        })
                        ->update(['is_read' => 1]);
                } else {
                    // 🔴 JIKA BELUM DIISI: Pastikan notifikasi pengingat ada & menyala!
                    $notifPernahDibuat = DB::table('notifications')
                        ->where('user_id', $guruId)
                        ->whereDate('created_at', $today)
                        ->where('is_read', 0)
                        ->where('message', 'like', "%{$jadwal->nama_kelas}%")
                        ->where(function($query) use ($jadwal, $firstMapelWord) {
                            $query->where('message', 'like', "%{$jadwal->nama_mapel}%")
                                  ->orWhere('message', 'like', "%{$firstMapelWord}%");
                        })
                        ->exists();

                    if (!$notifPernahDibuat) {
                        DB::table('notifications')->insert([
                            'user_id'    => $guruId,
                            'message'    => "Pengingat Otomatis: Anda belum mengisi jurnal mengajar kelas {$jadwal->nama_kelas} ({$jadwal->nama_mapel}) hari ini!",
                            'is_read'    => 0,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
        }

        // Ambil notifikasi aktif (khusus user ini & yang belum diisi jurnalnya)
        $notifications = DB::table('notifications')
            ->where('user_id', $guruId)
            ->where('is_read', 0)
            ->orderBy('created_at', 'desc')
            ->get();

        // 1. Total Kelas
        $totalKelas = DB::table('jadwals')
            ->where('guru_id', $guruId)
            ->distinct('kelas_id')
            ->count('kelas_id');

        // 2. Total Mapel
        $totalMapel = DB::table('jadwals')
            ->where('guru_id', $guruId)
            ->distinct('mapel_id')
            ->count('mapel_id');

        // 3. Jurnal Hari Ini
        $jurnalHariIni = DB::table('jurnals')
            ->where('guru_id', $guruId)
            ->whereDate('tanggal', today())
            ->count();

        // 4. QUERY UTAMA
        $queryJurnal = DB::table('jurnals')
            ->join('kelas_master', 'jurnals.kelas_id', '=', 'kelas_master.id')
            ->join('mapel_master', 'jurnals.mapel_id', '=', 'mapel_master.id')
            ->leftJoin('jadwals', function($join) use ($guruId) {
                $join->on('jurnals.kelas_id', '=', 'jadwals.kelas_id')
                     ->on('jurnals.mapel_id', '=', 'jadwals.mapel_id')
                     ->on('jurnals.jam_ke', '=', 'jadwals.jam_ke')
                     ->where('jadwals.guru_id', '=', $guruId)
                     ->on('jadwals.hari', '=', DB::raw("CASE DAYOFWEEK(jurnals.tanggal)
                        WHEN 1 THEN 'Minggu' WHEN 2 THEN 'Senin' WHEN 3 THEN 'Selasa'
                        WHEN 4 THEN 'Rabu' WHEN 5 THEN 'Kamis' WHEN 6 THEN 'Jumat' WHEN 7 THEN 'Sabtu'
                     END"));
            })
            ->where('jurnals.guru_id', $guruId)
            ->select(
                'jurnals.*', 
                'kelas_master.nama_kelas as nama_kelas', 
                'mapel_master.nama_mapel as nama_mapel',
                'jadwals.jam_mulai',
                'jadwals.jam_selesai'
            );

        // Filter Logic
        $currentFilter = $request->query('filter'); 
        $startDate = null;

        if ($request->filled('tanggal')) {
            $queryJurnal->whereDate('jurnals.tanggal', $request->tanggal);
            $currentFilter = 'custom';
        } elseif ($currentFilter) {
            if ($currentFilter === 'hari_ini') {
                $startDate = \Carbon\Carbon::today()->toDateString();
            } elseif ($currentFilter === '1_minggu') {
                $startDate = \Carbon\Carbon::now()->subWeek()->toDateString();
            } elseif ($currentFilter === '1_bulan') {
                $startDate = \Carbon\Carbon::now()->subMonth()->toDateString();
            }

            if ($startDate) {
                $queryJurnal->whereDate('jurnals.tanggal', '>=', $startDate);
            }
        }

        $jurnalTerbaru = $queryJurnal->orderBy('jurnals.created_at', 'desc')
            ->limit(10)
            ->get();

        return view('guru.dashboard-ringkasan', compact(
            'totalKelas', 
            'totalMapel', 
            'jurnalHariIni',
            'jurnalTerbaru',
            'notifications',
            'currentFilter'
        ));
    }

    /**
     * EXPORT RIWAYAT JURNAL KE EXCEL/CSV (Terstruktur & Rapi)
     */
    public function exportExcel(Request $request)
    {
        $guruId = session('guru_id') ?? session('admin_id');

        if (!$guruId) {
            return redirect()->route('login');
        }

        $guruName = session('user_name') ?? 'Guru';

        $queryJurnal = DB::table('jurnals')
            ->join('kelas_master', 'jurnals.kelas_id', '=', 'kelas_master.id')
            ->join('mapel_master', 'jurnals.mapel_id', '=', 'mapel_master.id')
            ->leftJoin('jadwals', function($join) use ($guruId) {
                $join->on('jurnals.kelas_id', '=', 'jadwals.kelas_id')
                     ->on('jurnals.mapel_id', '=', 'jadwals.mapel_id')
                     ->on('jurnals.jam_ke', '=', 'jadwals.jam_ke')
                     ->where('jadwals.guru_id', '=', $guruId)
                     ->on('jadwals.hari', '=', DB::raw("CASE DAYOFWEEK(jurnals.tanggal)
                        WHEN 1 THEN 'Minggu' WHEN 2 THEN 'Senin' WHEN 3 THEN 'Selasa'
                        WHEN 4 THEN 'Rabu' WHEN 5 THEN 'Kamis' WHEN 6 THEN 'Jumat' WHEN 7 THEN 'Sabtu'
                     END"));
            })
            ->where('jurnals.guru_id', $guruId)
            ->select(
                'jurnals.*', 
                'kelas_master.nama_kelas', 
                'mapel_master.nama_mapel',
                'jadwals.jam_mulai',
                'jadwals.jam_selesai'
            );

        // Filter Tanggal Sesuai Filter Aktif & Label Periode
        $currentFilter = $request->query('filter');
        $periodeLabel = 'Semua Periode';

        if ($request->filled('tanggal')) {
            $queryJurnal->whereDate('jurnals.tanggal', $request->tanggal);
            $periodeLabel = Carbon::parse($request->tanggal)->format('d/m/Y');
        } elseif ($currentFilter === 'hari_ini') {
            $queryJurnal->whereDate('jurnals.tanggal', Carbon::today());
            $periodeLabel = 'Hari Ini (' . Carbon::today()->format('d/m/Y') . ')';
        } elseif ($currentFilter === '1_minggu') {
            $queryJurnal->whereDate('jurnals.tanggal', '>=', Carbon::now()->subWeek());
            $periodeLabel = '1 Minggu Terakhir';
        } elseif ($currentFilter === '1_bulan') {
            $queryJurnal->whereDate('jurnals.tanggal', '>=', Carbon::now()->subMonth());
            $periodeLabel = '1 Bulan Terakhir';
        }

        $jurnals = $queryJurnal->orderBy('jurnals.tanggal', 'desc')->get();

        $fileName = 'Riwayat_Jurnal_Mengajar_' . date('Y-m-d_H-i') . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($jurnals, $guruName, $periodeLabel) {
            $file = fopen('php://output', 'w');
            
            // BOM UTF-8 (Supaya karakter rapi saat dibuka di Microsoft Excel)
            fputs($file, "\xEF\xBB\xBF");

            // 1. HEADER INFORMASI LAPORAN
            fputcsv($file, ['LAPORAN REKAPITULASI JURNAL MENGAJAR GURU']);
            fputcsv($file, ['Nama Guru', $guruName]);
            fputcsv($file, ['Tanggal Export', Carbon::now()->translatedFormat('d F Y H:i')]);
            fputcsv($file, ['Periode Laporan', $periodeLabel]);
            fputcsv($file, []); // Baris kosong pembatas

            // 2. HEADER TABEL UTAMA
            fputcsv($file, [
                'No', 
                'Tanggal Input', 
                'Tanggal KBM', 
                'Kelas', 
                'Mata Pelajaran', 
                'Waktu Sesi', 
                'Bahasan Materi', 
                'Target Berikutnya',
                'Ringkasan Kehadiran', 
                'Catatan Khusus Siswa'
            ]);

            $no = 1;
            foreach ($jurnals as $jurnal) {
                $waktuSesi = (!empty($jurnal->jam_mulai) && !empty($jurnal->jam_selesai)) 
                    ? Carbon::parse($jurnal->jam_mulai)->format('H:i') . ' - ' . Carbon::parse($jurnal->jam_selesai)->format('H:i')
                    : 'Jam ke-' . ($jurnal->jam_ke ?? '-');

                $students = json_decode($jurnal->student_ids, true);
                $hadirCount = 0;
                $sakitCount = 0;
                $izinCount = 0;
                $alphaCount = 0;
                $catatanArr = [];

                if (is_array($students)) {
                    foreach ($students as $s) {
                        $status = strtolower($s['status'] ?? 'hadir');
                        if ($status === 'hadir') $hadirCount++;
                        elseif ($status === 'sakit') $sakitCount++;
                        elseif ($status === 'izin') $izinCount++;
                        elseif ($status === 'alpha') $alphaCount++;

                        if (!empty($s['catatan'])) {
                            $siswaDb = DB::table('students')->where('id', $s['student_id'] ?? 0)->first();
                            $nama = $siswaDb ? $siswaDb->name : ('ID:' . ($s['student_id'] ?? '-'));
                            $catatanArr[] = $nama . ' (' . ucfirst($status) . '): "' . $s['catatan'] . '"';
                        }
                    }
                }

                $summaryAbsen = "Hadir: {$hadirCount}, Sakit: {$sakitCount}, Izin: {$izinCount}, Alpha: {$alphaCount}";
                $txtCatatan = count($catatanArr) > 0 ? implode(' | ', $catatanArr) : '-';

                fputcsv($file, [
                    $no++,
                    Carbon::parse($jurnal->created_at)->format('d/m/Y H:i'),
                    Carbon::parse($jurnal->tanggal)->format('d/m/Y'),
                    $jurnal->nama_kelas,
                    $jurnal->nama_mapel,
                    $waktuSesi,
                    $jurnal->materi ?? '-',
                    $jurnal->target_next ?? '-',
                    $summaryAbsen,
                    $txtCatatan
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}