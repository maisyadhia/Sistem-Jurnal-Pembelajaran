<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JadwalController extends Controller
{
    use LogsAdminActivity;

    public function index()
    {
        $jadwal = DB::table('jadwals')
            ->join('guru', 'jadwals.guru_id', '=', 'guru.id')
            ->join('kelas_master', 'jadwals.kelas_id', '=', 'kelas_master.id')
            ->join('mapel_master', 'jadwals.mapel_id', '=', 'mapel_master.id')
            ->select(
                'jadwals.*',
                'guru.nama_guru',
                'kelas_master.nama_kelas',
                'mapel_master.nama_mapel'
            )
            ->orderBy('jadwals.hari')
            ->orderBy('jadwals.jam_ke')
            ->get();

        // Grouping berdasarkan kombinasi guru-kelas-mapel-hari
        $groupedJadwal = $jadwal->groupBy(function($item) {
            return $item->hari . '|' . $item->guru_id . '|' . $item->kelas_id . '|' . $item->mapel_id;
        });

        return view('admin.data-master.jadwal', compact('groupedJadwal'));
    }

    public function create()
    {
        $guru = DB::table('guru')->orderBy('nama_guru')->get();
        $kelas = DB::table('kelas_master')->orderBy('nama_kelas')->get();
        $mapel = DB::table('mapel_master')->orderBy('nama_mapel')->get();
        
        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        $jamKe = range(1, 8);
        
        return view('admin.data-master.jadwal-create', compact('guru', 'kelas', 'mapel', 'hari', 'jamKe'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'guru_id' => 'required|exists:guru,id',
            'kelas_id' => 'required|exists:kelas_master,id',
            'mapel_id' => 'required|exists:mapel_master,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'jam_ke' => 'required|array|min:1',
            'jam_ke.*' => 'integer|min:1|max:10',
        ]);

        // Validasi manual untuk jam yang dicentang saja
        $errors = [];
        foreach ($request->jam_ke as $jam) {
            if (!isset($request->jam_mulai[$jam]) || empty($request->jam_mulai[$jam])) {
                $errors["jam_mulai.{$jam}"] = "Jam mulai untuk Jam {$jam} wajib diisi!";
            }
            if (!isset($request->jam_selesai[$jam]) || empty($request->jam_selesai[$jam])) {
                $errors["jam_selesai.{$jam}"] = "Jam selesai untuk Jam {$jam} wajib diisi!";
            }
        }

        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        $inserted = 0;
        $duplicateErrors = [];

        foreach ($request->jam_ke as $jam) {
            // Cek duplikasi
            $exists = DB::table('jadwals')
                ->where('guru_id', $request->guru_id)
                ->where('hari', $request->hari)
                ->where('jam_ke', $jam)
                ->exists();

            if ($exists) {
                $duplicateErrors[] = "Jam ke-{$jam} sudah terisi untuk guru ini di hari {$request->hari}";
                continue;
            }

            DB::table('jadwals')->insert([
                'guru_id' => $request->guru_id,
                'kelas_id' => $request->kelas_id,
                'mapel_id' => $request->mapel_id,
                'hari' => $request->hari,
                'jam_ke' => $jam,
                'jam_mulai' => $request->jam_mulai[$jam],
                'jam_selesai' => $request->jam_selesai[$jam],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $inserted++;
        }

        if ($inserted === 0) {
            return back()->withErrors(['jam_ke' => implode(', ', $duplicateErrors)])->withInput();
        }

        $this->logActivity(
            'create',
            'jadwal',
            "Menambahkan {$inserted} jadwal baru",
            null,
            $request->all()
        );

        $message = "{$inserted} jadwal berhasil ditambahkan!";
        if (!empty($duplicateErrors)) {
            $message .= " (" . implode(', ', $duplicateErrors) . ")";
        }

        return redirect()->route('data-master.jadwal')
            ->with('success', $message);
    }

    public function edit($id)
    {
        // Cari data jadwal berdasarkan ID
        $jadwal = DB::table('jadwals')
            ->join('guru', 'jadwals.guru_id', '=', 'guru.id')
            ->join('kelas_master', 'jadwals.kelas_id', '=', 'kelas_master.id')
            ->join('mapel_master', 'jadwals.mapel_id', '=', 'mapel_master.id')
            ->select(
                'jadwals.*',
                'guru.nama_guru',
                'kelas_master.nama_kelas',
                'mapel_master.nama_mapel'
            )
            ->where('jadwals.id', $id)
            ->first();

        if (!$jadwal) {
            return redirect()->route('data-master.jadwal')
                ->with('error', 'Jadwal tidak ditemukan!');
        }

        // Ambil SEMUA jadwal dengan kombinasi yang sama (guru, kelas, mapel, hari)
        $jadwalGroup = DB::table('jadwals')
            ->where('guru_id', $jadwal->guru_id)
            ->where('kelas_id', $jadwal->kelas_id)
            ->where('mapel_id', $jadwal->mapel_id)
            ->where('hari', $jadwal->hari)
            ->get();

        // Ambil daftar jam_ke dari group
        $jamKeList = $jadwalGroup->pluck('jam_ke')->toArray();

        // Ambil data untuk dropdown
        $guru = DB::table('guru')->orderBy('nama_guru')->get();
        $kelas = DB::table('kelas_master')->orderBy('nama_kelas')->get();
        $mapel = DB::table('mapel_master')->orderBy('nama_mapel')->get();
        
        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
        
        return view('admin.data-master.jadwal-edit', compact('jadwal', 'jadwalGroup', 'jamKeList', 'guru', 'kelas', 'mapel', 'hari'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'guru_id' => 'required|exists:guru,id',
            'kelas_id' => 'required|exists:kelas_master,id',
            'mapel_id' => 'required|exists:mapel_master,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
            'jam_ke' => 'required|array|min:1',
            'jam_ke.*' => 'integer|min:1|max:10',
        ]);

        // Validasi manual untuk jam yang dicentang saja
        $errors = [];
        foreach ($request->jam_ke as $jam) {
            if (!isset($request->jam_mulai[$jam]) || empty($request->jam_mulai[$jam])) {
                $errors["jam_mulai.{$jam}"] = "Jam mulai untuk Jam {$jam} wajib diisi!";
            }
            if (!isset($request->jam_selesai[$jam]) || empty($request->jam_selesai[$jam])) {
                $errors["jam_selesai.{$jam}"] = "Jam selesai untuk Jam {$jam} wajib diisi!";
            }
        }

        if (!empty($errors)) {
            return back()->withErrors($errors)->withInput();
        }

        // Ambil data jadwal LAMA berdasarkan ID
        $oldJadwal = DB::table('jadwals')
            ->join('guru', 'jadwals.guru_id', '=', 'guru.id')
            ->join('kelas_master', 'jadwals.kelas_id', '=', 'kelas_master.id')
            ->join('mapel_master', 'jadwals.mapel_id', '=', 'mapel_master.id')
            ->select('jadwals.*', 'guru.nama_guru', 'kelas_master.nama_kelas', 'mapel_master.nama_mapel')
            ->where('jadwals.id', $id)
            ->first();

        if (!$oldJadwal) {
            return redirect()->route('data-master.jadwal')
                ->with('error', 'Jadwal tidak ditemukan!');
        }

        // 🔥 HAPUS SEMUA jadwal dengan kombinasi LAMA (guru, kelas, mapel, hari dari data lama)
        DB::table('jadwals')
            ->where('guru_id', $oldJadwal->guru_id)
            ->where('kelas_id', $oldJadwal->kelas_id)
            ->where('mapel_id', $oldJadwal->mapel_id)
            ->where('hari', $oldJadwal->hari)
            ->delete();

        // 🔥 INSERT jadwal baru dengan kombinasi BARU
        $inserted = 0;
        foreach ($request->jam_ke as $jam) {
            DB::table('jadwals')->insert([
                'guru_id' => $request->guru_id,
                'kelas_id' => $request->kelas_id,
                'mapel_id' => $request->mapel_id,
                'hari' => $request->hari,
                'jam_ke' => $jam,
                'jam_mulai' => $request->jam_mulai[$jam],
                'jam_selesai' => $request->jam_selesai[$jam],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            $inserted++;
        }

        $this->logActivity(
            'update',
            'jadwal',
            "Mengupdate {$inserted} jadwal dari {$oldJadwal->nama_guru} - {$oldJadwal->nama_mapel} menjadi {$request->nama_guru} - {$request->mapel_id}",
            $oldJadwal,
            $request->all()
        );

        return redirect()->route('data-master.jadwal')
            ->with('success', "{$inserted} jadwal berhasil diupdate!");
    }

    public function destroy($id)
    {
        // Ambil data jadwal berdasarkan ID
        $jadwal = DB::table('jadwals')->where('id', $id)->first();
        
        if (!$jadwal) {
            return redirect()->route('data-master.jadwal')
                ->with('error', 'Jadwal tidak ditemukan!');
        }

        // 🔥 HAPUS SEMUA jadwal dengan kombinasi yang sama
        DB::table('jadwals')
            ->where('guru_id', $jadwal->guru_id)
            ->where('kelas_id', $jadwal->kelas_id)
            ->where('mapel_id', $jadwal->mapel_id)
            ->where('hari', $jadwal->hari)
            ->delete();

        $this->logActivity(
            'delete',
            'jadwal',
            "Menghapus jadwal untuk {$jadwal->guru_id} - {$jadwal->mapel_id}",
            $jadwal,
            null
        );

        return redirect()->route('data-master.jadwal')
            ->with('success', 'Jadwal berhasil dihapus!');
    }
}