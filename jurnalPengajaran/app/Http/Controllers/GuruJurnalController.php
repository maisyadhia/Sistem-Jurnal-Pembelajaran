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

        // Kunci hari aktif saat ini dalam format bahasa Indonesia 
        Carbon::setLocale('id');
        $hari = Carbon::now()->translatedFormat('l'); 
        $tanggal = Carbon::today();

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

        // 💡 3. OLAH JAM SESI AGAR PRESISI TERHADAP JADWAL TERPISAH (MISAL JAM 4 & 7)
        $jadwalPertama = $listJadwal->first();
        
        $jadwal = new \stdClass();
        $jadwal->nama_kelas = $jadwalPertama->nama_kelas;
        $jadwal->nama_mapel = $jadwalPertama->nama_mapel;

        // Ambil daftar nomor jam ke- (Contoh: "Jam ke-4 & 7")
        $listJamKe = $listJadwal->pluck('jam_ke')->unique()->toArray();
        $jadwal->jam_ke_text = 'Jam ke ' . implode(' & ', $listJamKe);

        // Cek apakah jam mengajar berurutan (misal 7, 8) atau terputus (misal 4, 7)
        $isSequential = true;
        $prevJam = null;
        
        foreach ($listJadwal as $j) {
            if ($prevJam !== null && $j->jam_ke != $prevJam + 1) {
                $isSequential = false; // Ada jeda/terputus
            }
            $prevJam = $j->jam_ke;
        }

        $waktuSesiArr = [];
        if ($isSequential && count($listJadwal) > 1) {
            // JIKA BERURUTAN (misal Jam 7 & 8) -> Tampilkan rentang gabung: "10.45 - 12.10"
            $jamMulai = Carbon::parse($jadwalPertama->jam_mulai)->format('H.i');
            $jamSelesai = Carbon::parse($listJadwal->last()->jam_selesai)->format('H.i');
            $jadwal->waktu_text = "{$jamMulai} - {$jamSelesai}";
            $jadwal->waktu_list = [$jadwal->waktu_text];
        } else {
            // JIKA TERPUTUS (misal Jam 4 & 7) ATAU CUMA 1 JAM -> Tuliskan jam masing-masing!
            foreach ($listJadwal as $j) {
                if (!empty($j->jam_mulai) && !empty($j->jam_selesai)) {
                    $m = Carbon::parse($j->jam_mulai)->format('H.i');
                    $s = Carbon::parse($j->jam_selesai)->format('H.i');
                    $waktuSesiArr[] = "{$m} - {$s}";
                }
            }
            $jadwal->waktu_text = implode(' & ', $waktuSesiArr);
            $jadwal->waktu_list = $waktuSesiArr;
        }

        $jadwal->is_sequential = $isSequential;

        // 4. Ambil daftar siswa yang berada di kelas ini
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
        // 1. Validasi Input Form (tanpa min:10 & next_target opsional)
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
        $hari = Carbon::now()->translatedFormat('l');

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

        // 3. Masukkan data ke tabel jurnals
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
            'tanggal'     => Carbon::today(),
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
        
        // 4. Redirect balik dengan pesan sukses
        if (session()->has('admin_id')) {
            return redirect()->route('monitoring')->with('success', 'Jurnal Berhasil Diinputkan oleh Admin!');
        }

        return redirect()->route('guru.dashboard')->with('success', 'Jurnal Berhasil Dikirim & Diarsipkan!');
    }
}