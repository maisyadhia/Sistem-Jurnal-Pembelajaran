<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuruJurnalController extends Controller
{
    public function index($kelas_id, $mapel_id)
    {
        // 1. Dapatkan guru_id dari session (bisa Guru atau Admin)
        $guruId = session('guru_id') ?? session('admin_id');
        
        if (!$guruId) {
            return redirect()->route('login')->withErrors('Sesi berakhir, silakan login kembali.');
        }

        // Kunci hari aktif saat ini untuk menjaga kedisiplinan 
        $hari = Carbon::now()->locale('id')->translatedFormat('l');
        $tanggal = Carbon::today();

        // 2. Cari jadwal yang COCOK antara Kelas, Mapel, DAN HARI INI
        $jadwal = DB::table('jadwals')
            ->join('kelas_master', 'jadwals.kelas_id', '=', 'kelas_master.id')
            ->join('mapel_master', 'jadwals.mapel_id', '=', 'mapel_master.id')
            ->where('jadwals.guru_id', $guruId)
            ->where('jadwals.kelas_id', $kelas_id)
            ->where('jadwals.mapel_id', $mapel_id) 
            ->where('jadwals.hari', $hari) // 🛡️ Penjaga kedisiplinan agar guru tidak molor/salah hari
            ->select('jadwals.*', 'kelas_master.nama_kelas', 'mapel_master.nama_mapel')
            ->first();

        // Jika tidak ada jadwal hari ini, langsung tolak balik ke dashboard 
        if (!$jadwal) {
            return redirect()->route('guru.dashboard')
                ->with('warning', 'Akses ditolak! Anda tidak memiliki jadwal mengajar aktif untuk kelas dan mata pelajaran ini pada hari ' . $hari);
        }

        // 3. Ambil daftar siswa yang berada di kelas ini (Aman karena $jadwal dipastikan valid)
        $daftar_siswa = DB::table('students')
            ->where('class', $jadwal->nama_kelas)
            ->orderBy('name')
            ->get();

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
        // 1. Validasi Input Form
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

        $guruId = session('guru_id') ?? session('admin_id');
        $hari = Carbon::now()->locale('id')->translatedFormat('l');

        // Cari jadwal mengajar untuk menentukan jam_ke secara otomatis
        $jadwal = DB::table('jadwals')
            ->where('guru_id', $guruId)
            ->where('kelas_id', $request->kelas_id)
            ->where('mapel_id', $request->mapel_id)
            ->where('hari', $hari)
            ->first();

        $jamKe = $jadwal ? $jadwal->jam_ke : ($request->jam_ke ?? 1);
        
        // 2. Proses data array absensi & catatan spesifik per siswa ke JSON
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

        // Cek apakah ada siswa yang absen (selain 'Hadir')
        $adaAbsen = 0;
        foreach ($laporanSiswa as $ls) {
            if ($ls['status'] !== 'Hadir') {
                $adaAbsen = 1;
                break;
            }
        }

        // 3. Masukkan data secara aman ke tabel jurnals
        DB::table('jurnals')->insert([
            'guru_id'     => $guruId,
            'kelas_id' => $request->kelas_id,
            'mapel_id' => $request->mapel_id,
            'student_ids' => !empty($laporanSiswa) ? json_encode($laporanSiswa) : null,
            'jam_ke' => $jamKe,
            'materi' => $request->topic,
            'target_next' => $request->next_target,
            'rpp_sesuai' => $request->has('rpp_completed') ? 1 : 0,
            'ada_absen' => $adaAbsen,
            'tanggal' => Carbon::today(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // 4. Pengalihan cerdas sesuai hak akses session login
        if (session()->has('admin_id')) {
            return redirect()->route('admin.monitoring')->with('success', 'Jurnal Berhasil Diinputkan oleh Admin!');
        }

        return redirect()->route('guru.dashboard')->with('success', 'Jurnal Berhasil Dikirim & Diarsipkan!');
    }
}