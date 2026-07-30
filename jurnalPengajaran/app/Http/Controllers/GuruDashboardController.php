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

        Carbon::setLocale('id'); 
        $namaHariIndo = Carbon::now()->translatedFormat('l'); 

        $jadwalHariIni = DB::table('jadwals')
            ->where('guru_id', $guruId)
            ->where('hari', $namaHariIndo)
            ->select('kelas_id', 'mapel_id')
            ->get();

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

                $cleanMapel = trim(preg_replace('/\s+/', ' ', str_replace(['(', ')'], '', $jadwal->nama_mapel)));
                $firstMapelWord = explode(' ', $cleanMapel)[0] ?? $jadwal->nama_mapel;

                if ($jurnalAda) {
                    // 💡 AKURAT: Matikan notifikasi jika jurnal kelas & mapel ini sudah diisi!
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
                    // JIKA BELUM DIISI: Buat notifikasi pengingat
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

        // Ambil notifikasi aktif yang belum dibaca (is_read = 0)
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

        // 4. QUERY UTAMA JURNAL
        $queryJurnal = DB::table('jurnals')
            ->join('kelas_master', 'jurnals.kelas_id', '=', 'kelas_master.id')
            ->join('mapel_master', 'jurnals.mapel_id', '=', 'mapel_master.id')
            ->where('jurnals.guru_id', $guruId)
            ->select(
                'jurnals.*', 
                'kelas_master.nama_kelas as nama_kelas', 
                'mapel_master.nama_mapel as nama_mapel'
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

        // 💡 5. OLAH WAKTU SESI LENGKAP UNTUK MASING-MASING ITEM RIWAYAT JURNAL
        foreach ($jurnalTerbaru as $j) {
            // Ambil nama hari dari tanggal jurnal
            Carbon::setLocale('id');
            $hariJurnal = Carbon::parse($j->tanggal)->translatedFormat('l');

            $jadwalMatching = DB::table('jadwals')
                ->where('guru_id', $guruId)
                ->where('kelas_id', $j->kelas_id)
                ->where('mapel_id', $j->mapel_id)
                ->where('hari', $hariJurnal)
                ->orderBy('jam_ke', 'asc')
                ->get();

            $waktuSesiArr = [];
            $jamKeList = [];

            if ($jadwalMatching->isNotEmpty()) {
                foreach ($jadwalMatching as $jm) {
                    $jamKeList[] = $jm->jam_ke;
                    if (!empty($jm->jam_mulai) && !empty($jm->jam_selesai)) {
                        $m = Carbon::parse($jm->jam_mulai)->format('H:i');
                        $s = Carbon::parse($jm->jam_selesai)->format('H:i');
                        $waktuSesiArr[] = "{$m} - {$s}";
                    }
                }
            }

            $j->waktu_sesi_list = $waktuSesiArr;
            $j->jam_ke_label = count($jamKeList) > 0 ? 'Jam ke ' . implode(' & ', array_unique($jamKeList)) : 'Jam ke-' . $j->jam_ke;
        }

        return view('guru.dashboard-ringkasan', compact(
            'totalKelas', 
            'totalMapel', 
            'jurnalHariIni',
            'jurnalTerbaru',
            'notifications',
            'currentFilter'
        ));
    }

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
            ->where('jurnals.guru_id', $guruId)
            ->select(
                'jurnals.*', 
                'kelas_master.nama_kelas', 
                'mapel_master.nama_mapel'
            );

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

        $callback = function() use ($jurnals, $guruName, $periodeLabel, $guruId) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, ['LAPORAN REKAPITULASI JURNAL MENGAJAR GURU']);
            fputcsv($file, ['Nama Guru', $guruName]);
            fputcsv($file, ['Tanggal Export', Carbon::now()->translatedFormat('d F Y H:i')]);
            fputcsv($file, ['Periode Laporan', $periodeLabel]);
            fputcsv($file, []);

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
                Carbon::setLocale('id');
                $hariJ = Carbon::parse($jurnal->tanggal)->translatedFormat('l');
                $jadwalMatch = DB::table('jadwals')
                    ->where('guru_id', $guruId)
                    ->where('kelas_id', $jurnal->kelas_id)
                    ->where('mapel_id', $jurnal->mapel_id)
                    ->where('hari', $hariJ)
                    ->get();

                $waktuArr = [];
                foreach ($jadwalMatch as $jm) {
                    if (!empty($jm->jam_mulai) && !empty($jm->jam_selesai)) {
                        $waktuArr[] = Carbon::parse($jm->jam_mulai)->format('H:i') . ' - ' . Carbon::parse($jm->jam_selesai)->format('H:i');
                    }
                }
                $waktuSesi = count($waktuArr) > 0 ? implode(' | ', $waktuArr) : ('Jam ke-' . ($jurnal->jam_ke ?? '-'));

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