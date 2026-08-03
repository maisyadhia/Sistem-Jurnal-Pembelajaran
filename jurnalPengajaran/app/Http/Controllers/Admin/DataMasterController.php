<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DataMasterController extends Controller
{
    public function index()
    {
        $totalGuru = DB::table('guru')->count();
        $totalSiswa = DB::table('students')->count();
        $totalKelas = DB::table('kelas_master')->count();
        $totalMapel = DB::table('mapel_master')->count();
        $totalJadwal = DB::table('jadwals')->count();
        
        return view('admin.data-master.index', compact(
            'totalGuru',
            'totalSiswa',
            'totalKelas',
            'totalMapel',
            'totalJadwal'
        ));
    }
}