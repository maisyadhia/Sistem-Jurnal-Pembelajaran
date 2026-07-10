<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuruJurnalController extends Controller
{
    public function index($kelas_id, $mapel_id)
    {
        $guruId = session('guru_id');

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

        // 2. Ambil daftar siswa yang berada di kelas ini untuk ditampilkan di dropdown multiselect
        $daftar_siswa = DB::table('students')
            ->where('class', $jadwal ? $jadwal->nama_kelas : '')
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
        $request->validate([
            'kelas_id' => 'required',
            'mapel_id' => 'required',
            'topic' => 'required|string|min:10',
            'next_target' => 'required|string|min:10',
        ], [
            'topic.required' => 'Bahasan hari ini wajib diisi woy!',
            'topic.min' => 'Bahasan hari ini minimal 10 karakter ya.',
            'next_target.required' => 'Target pertemuan berikutnya wajib diisi!',
        ]);

        $guruId = session('guru_id');
        $hari = Carbon::now()->locale('id')->translatedFormat('l');

        $jadwal = DB::table('jadwals')
            ->where('guru_id', $guruId)
            ->where('kelas_id', $request->kelas_id)
            ->where('mapel_id', $request->mapel_id)
            ->where('hari', $hari)
            ->first();

        // 💡 PROSES STRUKTUR DATA BARU (Menggabungkan ID, Status, dan Catatan Anak)
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

        // Cek apakah ada yang absen (Sakit/Izin/Alpha) untuk menggantikan checkbox lama
        $adaAbsen = 0;
        foreach ($laporanSiswa as $ls) {
            if ($ls['status'] !== 'Hadir') {
                $adaAbsen = 1;
                break;
            }
        }

        // Masukkan paket data utuh ke tabel jurnals
        DB::table('jurnals')->insert([
            'kelas_id' => $request->kelas_id,
            'mapel_id' => $request->mapel_id,
            
            // Kolom ini sekarang menyimpan list anak, status absen, dan catatan spesifiknya woy!
            'student_ids' => !empty($laporanSiswa) ? json_encode($laporanSiswa) : null, 
            
            'jam_ke' => $jadwal ? $jadwal->jam_ke : 1,
            'materi' => $request->topic,
            'target_next' => $request->next_target,
            'catatan_perkembangan' => null, // Sudah pindah ke dalam struktur JSON per siswa
            'rpp_sesuai' => $request->has('rpp_completed') ? 1 : 0,
            'ada_absen' => $adaAbsen, // Otomatis bernilai 1 jika ada anak yang tidak hadir
            'tanggal' => Carbon::today(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('guru.dashboard')->with('success', 'Jurnal Berhasil Dikirim & Diarsipkan!');
    }
}