<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuruJurnalController extends Controller
{
    public function index($kelas_id, $mapel_id)
    {
        // ðŸ’¡ KUNCI ZONA WAKTU KE ASIA/JAKARTA
        date_default_timezone_set('Asia/Jakarta');

        // 1. Dapatkan guru_id dari session (bisa Guru atau Admin)
        $guruId = session('guru_id') ?? session('admin_id');
        
        if (!$guruId) {
            return redirect()->route('login')->withErrors('Sesi berakhir, silakan login kembali.');
        }

        // Kunci hari aktif saat ini dalam format bahasa Indonesia (Asia/Jakarta)
        Carbon::setLocale('id');
        $now = Carbon::now('Asia/Jakarta');
        $hari = $now->translatedFormat('l'); 
        $tanggal = $now->toDateString();

        // 2. Cari SEMUA jadwal yang cocok untuk kelas, mapel, & hari ini
        $listJadwal = DB::table('jadwals')
            ->join('kelas_master', 'jadwals.kelas_id', '=', 'kelas_master.id')
            ->join('mapel_master', 'jadwals.mapel_id', '=', 'mapel_master.id')
            ->where('jadwals.guru_id', $guruId)
            ->where('jadwals.kelas_id', $kelas_id)
            ->where('jadwals.mapel_id', $mapel_id) 
            ->where('jadwals.hari', $hari)
            ->select('jadwals.*', 'kelas_master.nama_kelas', 'mapel_master.nama_mapel')
            ->orderBy('jadwals.jam_ke', 'asc')
            ->get();

        // Jika tidak ada jadwal hari ini, kembalikan ke dashboard dengan warning
        if ($listJadwal->isEmpty()) {
            return redirect()->route('guru.dashboard')
                ->with('warning', 'Akses ditolak! Anda tidak memiliki jadwal mengajar aktif untuk kelas dan mata pelajaran ini pada hari ' . $hari . ' !');
        }

        // 3. SUSUN WAKTU SESI
        $jadwalPertama = $listJadwal->first();
        
        $jadwal = new \stdClass();
        $jadwal->nama_kelas = $jadwalPertama->nama_kelas;
        $jadwal->nama_mapel = $jadwalPertama->nama_mapel;

        $waktuList = [];
        $listJamKe = [];

        foreach ($listJadwal as $j) {
            $listJamKe[] = $j->jam_ke;

            if (!empty($j->jam_mulai) && !empty($j->jam_selesai)) {
                $m = Carbon::parse($j->jam_mulai)->format('H:i');
                $s = Carbon::parse($j->jam_selesai)->format('H:i');
                $waktuList[] = "{$m} - {$s}";
            } else {
                $waktuList[] = "Jam ke-{$j->jam_ke}";
            }
        }

        $jadwal->waktu_list = $waktuList;
        $uniqueJam = array_unique($listJamKe);
        $jadwal->jam_ke_text = 'Jam ke ' . implode(' & ', $uniqueJam);

        // 4. Ambil daftar siswa
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
        // ðŸ’¡ KUNCI ZONA WAKTU KE ASIA/JAKARTA
        date_default_timezone_set('Asia/Jakarta');

        $request->validate([
            'kelas_id'    => 'required',
            'mapel_id'    => 'required',
            'topic'       => 'required|string',
            'next_target' => 'nullable|string',
        ], [
            'topic.required' => 'Bahasan hari ini wajib diisi!',
        ]);

        $guruId = session('guru_id') ?? session('admin_id');
        
        Carbon::setLocale('id');
        $now = Carbon::now('Asia/Jakarta');
        $hari = $now->translatedFormat('l');

        $jadwal = DB::table('jadwals')
            ->where('guru_id', $guruId)
            ->where('kelas_id', $request->kelas_id)
            ->where('mapel_id', $request->mapel_id)
            ->where('hari', $hari)
            ->first();

        $jamKe = $jadwal ? $jadwal->jam_ke : ($request->jam_ke ?? 1);
        
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

        $tanggalJurnal = $now->toDateString();
        if ($request->filled('tanggal_mengajar')) {
            try {
                $tanggalJurnal = Carbon::createFromFormat('d-m-Y', $request->tanggal_mengajar, 'Asia/Jakarta')->format('Y-m-d');
            } catch (\Exception $e) {
                $tanggalJurnal = $request->tanggal_mengajar;
            }
        }

        DB::table('jurnals')->insert([
            'guru_id'     => $guruId,
            'kelas_id'    => $request->kelas_id,
            'mapel_id'    => $request->mapel_id,
            'student_ids' => !empty($laporanSiswa) ? json_encode($laporanSiswa) : null,
            'jam_ke'      => $jamKe,
            'materi'      => $request->topic,
            'target_next' => $request->next_target,
            'rpp_sesuai'  => $request->has('rpp_completed') ? 1 : 0,
            'ada_absen'   => $adaAbsen,
            'tanggal'     => $tanggalJurnal,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
        
        if (session()->has('admin_id')) {
            return redirect()->route('monitoring')->with('success', 'Jurnal Berhasil Diinputkan oleh Admin!');
        }

        return redirect()->route('guru.dashboard')->with('success', 'Jurnal Berhasil Dikirim & Diarsipkan!');
    }

    // FITUR EDIT: HALAMAN FORM EDIT JURNAL (BISA DIEDIT KAPAN SAJA)
    public function edit($id)
    {
        $guruId = session('guru_id') ?? session('admin_id');
        if (!$guruId) {
            return redirect()->route('login');
        }

        // 1. Cari jurnal berdasarkan ID dan kepemilikan guru (Tanpa proteksi hari mengajar)
        $jurnal = DB::table('jurnals')->where('id', $id)->where('guru_id', $guruId)->first();
        
        if (!$jurnal) {
            return redirect()->route('guru.dashboard')->with('warning', 'Akses ditolak atau data jurnal tidak ditemukan!');
        }

        // 2. Ambil nama kelas & mapel langsung dari kelas_master & mapel_master tanpa tergantung tabel jadwals hari ini
        $namaKelas = DB::table('kelas_master')->where('id', $jurnal->kelas_id)->value('nama_kelas');
        $namaMapel = DB::table('mapel_master')->where('id', $jurnal->mapel_id)->value('nama_mapel');

        $jadwal = new \stdClass();
        $jadwal->nama_kelas = $namaKelas ?? 'Kelas -';
        $jadwal->nama_mapel = $namaMapel ?? 'Mapel -';

        return view('guru.jurnal-edit', compact('jurnal', 'jadwal'));
    }

    // FITUR EDIT: UPDATE KHUSUS MATERI & TARGET
    public function update(Request $request, $id)
    {
        $request->validate([
            'topic' => 'required|string',
        ], [
            'topic.required' => 'Bahasan materi wajib diisi!',
        ]);

        $guruId = session('guru_id') ?? session('admin_id');

        // Pastikan jurnal milik guru yang login
        $jurnal = DB::table('jurnals')->where('id', $id)->where('guru_id', $guruId)->first();

        if (!$jurnal) {
            return redirect()->route('guru.dashboard')->with('warning', 'Akses ditolak!');
        }

        // Update HANYA materi, target_next, dan timestamp updated_at
        DB::table('jurnals')->where('id', $id)->update([
            'materi'      => $request->topic,
            'target_next' => $request->next_target,
            'updated_at'  => now(),
        ]);

        return redirect()->route('guru.dashboard')->with('success', 'Materi & Target Jurnal berhasil diperbarui!');
    }
}