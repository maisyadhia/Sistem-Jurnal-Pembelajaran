<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class GuruJurnalController extends Controller
{
    public function index($kelas_id, $mapel_id)
    {
        // Coba dapatkan guru_id dari session
        $guruId = session('guru_id');
        
        // Jika tidak ada, coba dari admin session
        if (!$guruId) {
            $guruId = session('admin_id');
        }
        
        // Jika masih tidak ada, redirect ke login
        if (!$guruId) {
            return redirect()->route('login')->withErrors('Sesi berakhir, silakan login kembali.');
        }

        $hari = Carbon::now()
                ->locale('id')
                ->translatedFormat('l');

        $tanggal = Carbon::today();

        // 1. Ambil data jadwal mengajar aktif beserta nama kelas dan mapel
        $jadwal = DB::table('jadwals')
            ->join('kelas_master', 'jadwals.kelas_id', '=', 'kelas_master.id')
            ->join('mapel_master', 'jadwals.mapel_id', '=', 'mapel_master.id')
            ->where('jadwals.guru_id', $guruId)
            ->where('jadwals.kelas_id', $kelas_id)
            ->where('jadwals.mapel_id', $mapel_id) 
            ->where('jadwals.hari', $hari)
            ->first();

        // 2. Ambil daftar siswa yang berada di kelas ini
        $daftar_siswa = DB::table('students')
            ->where('class', $jadwal ? $jadwal->nama_kelas : '')
            ->orderBy('name')
            ->get();

        // Jika tidak ada jadwal, redirect ke dashboard guru
        if (!$jadwal) {
            return redirect()->route('guru.dashboard')
                ->with('warning', 'Tidak ada jadwal untuk kelas dan mata pelajaran ini pada hari ' . $hari);
        }

        return view(
            'guru.jurnal',
            compact(
                'jadwal',
                'tanggal',
                'hari',
                'daftar_siswa'
            )
        );
    }

    public function store(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required',
            'mapel_id' => 'required',
            'topic' => 'required|string|min:10',
            'next_target' => 'required|string|min:10',
        ], [
            'topic.required' => 'Bahasan hari ini wajib diisi!',
            'topic.min' => 'Bahasan hari ini minimal 10 karakter!',
            'next_target.required' => 'Target pertemuan berikutnya wajib diisi!',
        ]);

        $guruId = session('guru_id');
        if (!$guruId) {
            $guruId = session('admin_id');
        }

        $hari = Carbon::now()->locale('id')->translatedFormat('l');

        $jadwal = DB::table('jadwals')
            ->where('guru_id', $guruId)
            ->where('kelas_id', $request->kelas_id)
            ->where('mapel_id', $request->mapel_id)
            ->where('hari', $hari)
            ->first();

        // Proses data siswa
        $laporanSiswa = [];
        if ($request->has('student_ids')) {
            foreach ($request->student_ids as $s_id) {
                $laporanSiswa[] = [
                    'student_id' => (int)$s_id,
                    'status'     => $request->status[$s_id] ?? 'Hadir',
                    'catatan'    => $request->notes[$s_id] ?? null
                ];
            }
        }

        $adaAbsen = 0;
        foreach ($laporanSiswa as $ls) {
            if ($ls['status'] !== 'Hadir') {
                $adaAbsen = 1;
                break;
            }
        }

        DB::table('jurnals')->insert([
            'kelas_id' => $request->kelas_id,
            'mapel_id' => $request->mapel_id,
            'student_ids' => !empty($laporanSiswa) ? json_encode($laporanSiswa) : null,
            'jam_ke' => $jadwal ? $jadwal->jam_ke : 1,
            'materi' => $request->topic,
            'target_next' => $request->next_target,
            'catatan_perkembangan' => null,
            'rpp_sesuai' => $request->has('rpp_completed') ? 1 : 0,
            'ada_absen' => $adaAbsen,
            'tanggal' => Carbon::today(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('guru.dashboard')->with('success', 'Jurnal Berhasil Dikirim & Diarsipkan!');
    }
}