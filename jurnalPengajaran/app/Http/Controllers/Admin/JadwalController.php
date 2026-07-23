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
                'mapel_master.nama_mapel',
                'mapel_master.kode_mapel'
            )
            ->orderBy('jadwals.hari')
            ->orderBy('jadwals.jam_ke')
            ->get();

        return view('admin.data-master.jadwal', compact('jadwal'));
    }

    public function create()
    {
        $guru = DB::table('guru')->orderBy('nama_guru')->get();
        $kelas = DB::table('kelas_master')->orderBy('nama_kelas')->get();
        $mapel = DB::table('mapel_master')->orderBy('nama_mapel')->get();
        
        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $jamKe = range(1, 8);
        
        return view('admin.data-master.jadwal-create', compact('guru', 'kelas', 'mapel', 'hari', 'jamKe'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'guru_id' => 'required|exists:guru,id',
            'kelas_id' => 'required|exists:kelas_master,id',
            'mapel_id' => 'required|exists:mapel_master,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_ke' => 'required|integer|min:1|max:8',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
        ]);

        // Cek duplikasi jadwal
        $exists = DB::table('jadwals')
            ->where('guru_id', $request->guru_id)
            ->where('hari', $request->hari)
            ->where('jam_ke', $request->jam_ke)
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'jam_ke' => 'Guru sudah memiliki jadwal di hari dan jam yang sama!'
            ])->withInput();
        }

        $data = [
            'guru_id' => $request->guru_id,
            'kelas_id' => $request->kelas_id,
            'mapel_id' => $request->mapel_id,
            'hari' => $request->hari,
            'jam_ke' => $request->jam_ke,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('jadwals')->insert($data);

        // Ambil nama untuk log
        $guru = DB::table('guru')->where('id', $request->guru_id)->first();
        $kelas = DB::table('kelas_master')->where('id', $request->kelas_id)->first();
        $mapel = DB::table('mapel_master')->where('id', $request->mapel_id)->first();

        $this->logActivity(
            'create',
            'jadwal',
            "Menambahkan jadwal: {$mapel->nama_mapel} - {$kelas->nama_kelas} ({$request->hari} Jam ke-{$request->jam_ke}) untuk {$guru->nama_guru}",
            null,
            $data
        );

        return redirect()->route('data-master.jadwal')
            ->with('success', 'Jadwal berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $jadwal = DB::table('jadwals')->where('id', $id)->first();
        if (!$jadwal) {
            return redirect()->route('data-master.jadwal')
                ->with('error', 'Jadwal tidak ditemukan!');
        }

        $guru = DB::table('guru')->orderBy('nama_guru')->get();
        $kelas = DB::table('kelas_master')->orderBy('nama_kelas')->get();
        $mapel = DB::table('mapel_master')->orderBy('nama_mapel')->get();
        
        $hari = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        $jamKe = range(1, 8);
        
        return view('admin.data-master.jadwal-edit', compact('jadwal', 'guru', 'kelas', 'mapel', 'hari', 'jamKe'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'guru_id' => 'required|exists:guru,id',
            'kelas_id' => 'required|exists:kelas_master,id',
            'mapel_id' => 'required|exists:mapel_master,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_ke' => 'required|integer|min:1|max:8',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
        ]);

        // Cek duplikasi jadwal (kecuali dirinya sendiri)
        $exists = DB::table('jadwals')
            ->where('guru_id', $request->guru_id)
            ->where('hari', $request->hari)
            ->where('jam_ke', $request->jam_ke)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()->withErrors([
                'jam_ke' => 'Guru sudah memiliki jadwal di hari dan jam yang sama!'
            ])->withInput();
        }

        $oldData = DB::table('jadwals')->where('id', $id)->first();

        $data = [
            'guru_id' => $request->guru_id,
            'kelas_id' => $request->kelas_id,
            'mapel_id' => $request->mapel_id,
            'hari' => $request->hari,
            'jam_ke' => $request->jam_ke,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'updated_at' => now(),
        ];

        DB::table('jadwals')->where('id', $id)->update($data);

        $this->logActivity(
            'update',
            'jadwal',
            "Mengupdate jadwal ID: {$id}",
            $oldData,
            $data
        );

        return redirect()->route('data-master.jadwal')
            ->with('success', 'Jadwal berhasil diupdate!');
    }

    public function destroy($id)
    {
        $jadwal = DB::table('jadwals')->where('id', $id)->first();
        
        DB::table('jadwals')->where('id', $id)->delete();

        $this->logActivity(
            'delete',
            'jadwal',
            "Menghapus jadwal ID: {$id}",
            $jadwal,
            null
        );

        return redirect()->route('data-master.jadwal')
            ->with('success', 'Jadwal berhasil dihapus!');
    }
}