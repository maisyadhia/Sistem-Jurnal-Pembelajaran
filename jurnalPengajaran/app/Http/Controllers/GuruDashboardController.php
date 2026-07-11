<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class GuruDashboardController extends Controller
{
    public function index()
    {
        $guruId = session('guru_id');

        if (!$guruId) {
            // Try to get from admin session if using Admin model
            if (session('admin_id')) {
                $guruId = session('admin_id');
            } else {
                return redirect()->route('login');
            }
        }

        // 1. Ambil daftar kelas unik untuk $daftar_kelas
        $daftar_kelas = DB::table('jadwals')
            ->join('kelas_master', 'jadwals.kelas_id', '=', 'kelas_master.id')
            ->where('jadwals.guru_id', $guruId)
            ->select('kelas_master.id as id', 'kelas_master.nama_kelas as nama_kelas')
            ->distinct()
            ->orderBy('kelas_master.nama_kelas')
            ->get();

        // 2. Ambil daftar mapel unik untuk $daftar_mapel
        $daftar_mapel = DB::table('jadwals')
            ->join('mapel_master', 'jadwals.mapel_id', '=', 'mapel_master.id')
            ->where('jadwals.guru_id', $guruId)
            ->select('mapel_master.id as id', 'mapel_master.nama_mapel as nama_mapel')
            ->distinct()
            ->orderBy('mapel_master.nama_mapel')
            ->get();

        // Jika tidak ada data jadwal, tampilkan pesan
        if ($daftar_kelas->isEmpty()) {
            return view('guru.dashboard', compact('daftar_kelas', 'daftar_mapel'))
                ->with('warning', 'Belum ada jadwal yang ditugaskan untuk Anda.');
        }

        return view('guru.dashboard', compact('daftar_kelas', 'daftar_mapel'));
    }
}