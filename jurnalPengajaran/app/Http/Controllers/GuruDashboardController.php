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

        // Ambil notifikasi untuk guru ini
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

        // 🛡️ 4. PERBAIKAN QUERY UTAMA: Kunci Left Join dengan 'hari' dan Hapus groupBy
        // Ambil nama hari bahasa Indonesia untuk mencocokkan jadwal asal jurnal tersebut 
        \Carbon\Carbon::setLocale('id');

        $queryJurnal = DB::table('jurnals')
            ->join('kelas_master', 'jurnals.kelas_id', '=', 'kelas_master.id')
            ->join('mapel_master', 'jurnals.mapel_id', '=', 'mapel_master.id')
            ->leftJoin('jadwals', function($join) use ($guruId) {
                $join->on('jurnals.kelas_id', '=', 'jadwals.kelas_id')
                     ->on('jurnals.mapel_id', '=', 'jadwals.mapel_id')
                     ->where('jadwals.guru_id', '=', $guruId)
                     // 💡 KUNCI DI SINI: Samakan hari di jadwal dengan hari input jurnal (dari created_at)
                     ->on('jadwals.hari', '=', DB::raw("CASE DAYOFWEEK(jurnals.created_at)
                        WHEN 1 THEN 'Minggu'
                        WHEN 2 THEN 'Senin'
                        WHEN 3 THEN 'Selasa'
                        WHEN 4 THEN 'Rabu'
                        WHEN 5 THEN 'Kamis'
                        WHEN 6 THEN 'Jumat'
                        WHEN 7 THEN 'Sabtu'
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

        // Filter tanggal non-JS
        if ($request->filled('tanggal')) {
            $queryJurnal->whereDate('jurnals.tanggal', $request->tanggal);
        }

        // 💡 groupBy Dihapus total agar tidak memicu Syntax Error / Access Violation !
        $jurnalTerbaru = $queryJurnal->orderBy('jurnals.created_at', 'desc')
            ->limit(10)
            ->get();

        return view('guru.dashboard-ringkasan', compact(
            'totalKelas', 
            'totalMapel', 
            'jurnalHariIni',
            'jurnalTerbaru',
            'notifications'
        ));
    }
}