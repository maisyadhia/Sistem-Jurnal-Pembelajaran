<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

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
            return view('guru.pilih-sesi', compact('daftar_kelas', 'daftar_mapel'))
                ->with('warning', 'Belum ada jadwal yang ditugaskan untuk Anda.');
        }

        return view('guru.pilih-sesi', compact('daftar_kelas', 'daftar_mapel'));
    }

    // Dashboard Ringkasan (route: guru.dashboard)
    public function dashboard()
    {
        $guruId = session('guru_id');
        
        if (!$guruId) {
            if (session('admin_id')) {
                $guruId = session('admin_id');
            } else {
                return redirect()->route('login');
            }
        }

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

        // 3. Jurnal Hari Ini - PERBAIKI: gunakan guru_id dan tanggal
        $jurnalHariIni = DB::table('jurnals')
            ->where('guru_id', $guruId)
            ->whereDate('tanggal', today())
            ->count();

        // 4. Jurnal Terbaru - PERBAIKI: gunakan guru_id dan tanggal
        $jurnalTerbaru = DB::table('jurnals')
            ->where('guru_id', $guruId)
            ->orderBy('tanggal', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('guru.dashboard-ringkasan', compact(
            'totalKelas', 
            'totalMapel', 
            'jurnalHariIni',
            'jurnalTerbaru'
        ));
    }
}