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
        $totalKelas = DB::table('kelas_master')->count();
        $totalMapel = DB::table('mapel_master')->count();
        
        return view('admin.data-master.index', compact(
            'totalGuru',
            'totalKelas',
            'totalMapel'
        ));
    }
}