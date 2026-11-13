<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GuruJurnalController extends Controller
{
    public function index(Request $request, $kelas_id, $mapel_id)
    {
        // KUNCI ZONA WAKTU KE ASIA/JAKARTA
        date_default_timezone_set('Asia/Jakarta');

        // 1. Dapatkan guru_id dari session (bisa Guru atau Admin)
        $guruId = session('guru_id') ?? session('admin_id');

        if (!$guruId) {
            return redirect()->route('login')->withErrors('Sesi berakhir, silakan login kembali.');
        }

        Carbon::setLocale('id');

        // Tangkap parameter tanggal dari URL (?tanggal=...) jika diklik dari notifikasi telat/lampau
        $tanggalParam = $request->query('tanggal');
        if ($tanggalParam) {
            try {
                $now = Carbon::createFromFormat('Y-m-d', $tanggalParam, 'Asia/Jakarta');
            } catch (\Exception $e) {
                $now = Carbon::now('Asia/Jakarta');
            }
        } else {
            $now = Carbon::now('Asia/Jakarta');
        }

        $tanggal = $now->toDateString();
        $hari = $now->translatedFormat('l');

        // Cari jadwal berdasarkan hari dari tanggal KBM target
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

        // Fallback pencocokan tanpa filter hari jika data spesifik hari kosong
        if ($listJadwal->isEmpty()) {
            $listJadwal = DB::table('jadwals')
                ->join('kelas_master', 'jadwals.kelas_id', '=', 'kelas_master.id')
                ->join('mapel_master', 'jadwals.mapel_id', '=', 'mapel_master.id')
                ->where('jadwals.guru_id', $guruId)
                ->where('jadwals.kelas_id', $kelas_id)
                ->where('jadwals.mapel_id', $mapel_id)
                ->select('jadwals.*', 'kelas_master.nama_kelas', 'mapel_master.nama_mapel')
                ->orderBy('jadwals.jam_ke', 'asc')
                ->get();
        }

        if ($listJadwal->isEmpty()) {
            return redirect()->route('guru.dashboard')
                ->with('warning', 'Akses ditolak! Anda tidak memiliki jadwal mengajar aktif untuk kelas dan mata pelajaran ini.');
        }

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
        date_default_timezone_set('Asia/Jakarta');

        $request->validate([
            'kelas_id'    => 'required',
            'mapel_id'    => 'required',
            'topic'       => 'required|string',
            'next_target' => 'required|string', 
        ], [
            'topic.required'       => 'Lengkapi Rangkuman Pengajaran !',
            'next_target.required' => 'Target pertemuan hari ini wajib diisi!',
        ]);

        $guruId = session('guru_id') ?? session('admin_id');
        
        Carbon::setLocale('id');

        // AMBIL TANGGAL KBM DARI HIDDEN INPUT (YANG DIBAWA DARI NOTIFIKASI)
        $tanggalJurnal = Carbon::now('Asia/Jakarta')->toDateString();
        if ($request->filled('tanggal_mengajar')) {
            try {
                $tanggalJurnal = Carbon::parse($request->tanggal_mengajar, 'Asia/Jakarta')->format('Y-m-d');
            } catch (\Exception $e) {
                $tanggalJurnal = $request->tanggal_mengajar;
            }
        }

        $hari = Carbon::parse($tanggalJurnal)->translatedFormat('l');

        $jadwal = DB::table('jadwals')
            ->where('guru_id', $guruId)
            ->where('kelas_id', $request->kelas_id)
            ->where('mapel_id', $request->mapel_id)
            ->where('hari', $hari)
            ->first();

        if (!$jadwal) {
            $jadwal = DB::table('jadwals')
                ->where('guru_id', $guruId)
                ->where('kelas_id', $request->kelas_id)
                ->where('mapel_id', $request->mapel_id)
                ->first();
        }

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

        // 1. SIMPAN DATA JURNAL KE DATABASE
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
        
        // 🔥 2. OTOMATIS UPDATE STATUS NOTIFIKASI DI DATABASE MENJADI is_read = 1
        $namaKelasTarget = DB::table('kelas_master')->where('id', $request->kelas_id)->value('nama_kelas');
        $namaMapelTarget = DB::table('mapel_master')->where('id', $request->mapel_id)->value('nama_mapel');

        if ($namaKelasTarget && $namaMapelTarget) {
            DB::table('notifications')
                ->where('user_id', $guruId)
                ->where('is_read', 0)
                ->whereDate('created_at', $tanggalJurnal)
                ->where('message', 'like', "%kelas {$namaKelasTarget}%")
                ->where('message', 'like', "%({$namaMapelTarget})%")
                ->update([
                    'is_read'    => 1,
                    'updated_at' => now()
                ]);
        }

        if (session()->has('admin_id')) {
            return redirect()->route('monitoring')->with('success', 'Jurnal Berhasil Diinputkan oleh Admin!');
        }

        return redirect()->route('guru.dashboard')->with('success', 'Jurnal Berhasil Dikirim & Diarsipkan!');
    }

    public function edit($id)
    {
        $guruId = session('guru_id') ?? session('admin_id');
        if (!$guruId) {
            return redirect()->route('login');
        }

        $jurnal = DB::table('jurnals')->where('id', $id)->where('guru_id', $guruId)->first();

        if (!$jurnal) {
            return redirect()->route('guru.dashboard')->with('warning', 'Akses ditolak atau data jurnal tidak ditemukan!');
        }

        $namaKelas = DB::table('kelas_master')->where('id', $jurnal->kelas_id)->value('nama_kelas');
        $namaMapel = DB::table('mapel_master')->where('id', $jurnal->mapel_id)->value('nama_mapel');

        $jadwal = new \stdClass();
        $jadwal->nama_kelas = $namaKelas ?? 'Kelas -';
        $jadwal->nama_mapel = $namaMapel ?? 'Mapel -';

        return view('guru.jurnal-edit', compact('jurnal', 'jadwal'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'topic' => 'required|string',
            'next_target' => 'required|string',
        ], [
            'topic.required' => 'Bahasan materi wajib diisi!',
            'next_target.required' => 'Target pertemuan berikutnya wajib diisi!',
        ]);

        $guruId = session('guru_id') ?? session('admin_id');

        $jurnal = DB::table('jurnals')->where('id', $id)->where('guru_id', $guruId)->first();

        if (!$jurnal) {
            return redirect()->route('guru.dashboard')->with('warning', 'Akses ditolak!');
        }

        DB::table('jurnals')->where('id', $id)->update([
            'materi' => $request->topic,
            'target_next' => $request->next_target,
            'updated_at' => now(),
        ]);

        return redirect()->route('guru.dashboard')->with('success', 'Materi & Target Jurnal berhasil diperbarui!');
    }
}