<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\GuruImport;
use App\Imports\SiswaImport;
use App\Imports\KelasImport;
use App\Imports\MapelImport;
use App\Imports\JadwalImport;
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

    // ====== IMPORT ======
    public function importGuru(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120'
        ]);

        try {
            $import = new GuruImport();
            Excel::import($import, $request->file('file'));

            $this->logActivity('import', 'guru', 'Import data guru via Excel');

            return back()->with('success', 'Data guru berhasil diimport!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    public function importSiswa(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120'
        ]);

        try {
            Excel::import(new SiswaImport(), $request->file('file'));
            $this->logActivity('import', 'siswa', 'Import data siswa via Excel');
            return back()->with('success', 'Data siswa berhasil diimport!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    public function importKelas(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120'
        ]);

        try {
            Excel::import(new KelasImport(), $request->file('file'));
            $this->logActivity('import', 'kelas', 'Import data kelas via Excel');
            return back()->with('success', 'Data kelas berhasil diimport!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    public function importMapel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120'
        ]);

        try {
            Excel::import(new MapelImport(), $request->file('file'));
            $this->logActivity('import', 'mapel', 'Import data mapel via Excel');
            return back()->with('success', 'Data mapel berhasil diimport!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    public function importJadwal(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:5120'
        ]);

        try {
            Excel::import(new JadwalImport(), $request->file('file'));
            $this->logActivity('import', 'jadwal', 'Import data jadwal via Excel');
            return back()->with('success', 'Data jadwal berhasil diimport!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import data: ' . $e->getMessage());
        }
    }

    // ====== EXPORT ======
    public function exportGuru()
    {
        $this->logActivity('export', 'guru', 'Export data guru ke Excel');
        return Excel::download(new GuruExport(), 'guru_' . date('Y-m-d') . '.xlsx');
    }

    public function exportSiswa()
    {
        $this->logActivity('export', 'siswa', 'Export data siswa ke Excel');
        return Excel::download(new SiswaExport(), 'siswa_' . date('Y-m-d') . '.xlsx');
    }

    public function exportKelas()
    {
        $this->logActivity('export', 'kelas', 'Export data kelas ke Excel');
        return Excel::download(new KelasExport(), 'kelas_' . date('Y-m-d') . '.xlsx');
    }

    public function exportMapel()
    {
        $this->logActivity('export', 'mapel', 'Export data mapel ke Excel');
        return Excel::download(new MapelExport(), 'mapel_' . date('Y-m-d') . '.xlsx');
    }

    public function exportJadwal()
    {
        $this->logActivity('export', 'jadwal', 'Export data jadwal ke Excel');
        return Excel::download(new JadwalExport(), 'jadwal_' . date('Y-m-d') . '.xlsx');
    }
}