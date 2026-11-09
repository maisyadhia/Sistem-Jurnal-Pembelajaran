<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\KelasMaster;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KelasController extends Controller
{
    use LogsAdminActivity;

    public function index()
    {
        // Pakai Eloquent dengan relasi
        $kelas = KelasMaster::with('waliKelas')->orderBy('nama_kelas')->get();
        return view('admin.data-master.kelas', compact('kelas'));
    }

    public function create()
    {
        // Ambil data guru untuk dropdown
        $guru = DB::table('guru')->orderBy('nama_guru')->get();
        return view('admin.data-master.kelas-create', compact('guru'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|unique:kelas_master,nama_kelas',
            'wali_kelas_id' => 'nullable|exists:guru,id', // Validasi ID Guru
        ]);

        $data = [
            'nama_kelas' => $request->nama_kelas,
            'wali_kelas_id' => $request->wali_kelas_id,
            'tahun_ajaran' => config('tahun_ajaran.aktif'),
            'is_aktif' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('kelas_master')->insert($data);

        // Ambil nama wali untuk log
        $wali = $request->wali_kelas_id ? DB::table('guru')->where('id', $request->wali_kelas_id)->first() : null;
        $namaWali = $wali ? $wali->nama_guru : 'Tidak ada';

        $this->logActivity(
            'create',
            'kelas',
            "Menambahkan kelas baru: {$request->nama_kelas} (Wali: {$namaWali})",
            null,
            $data
        );

        return redirect()->route('data-master.kelas')
            ->with('success', 'Kelas berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $kelas = DB::table('kelas_master')->where('id', $id)->first();
        if (!$kelas) {
            return redirect()->route('data-master.kelas')
                ->with('error', 'Kelas tidak ditemukan!');
        }
        
        // Ambil data guru untuk dropdown
        $guru = DB::table('guru')->orderBy('nama_guru')->get();
        
        return view('admin.data-master.kelas-edit', compact('kelas', 'guru'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kelas' => 'required|unique:kelas_master,nama_kelas,' . $id,
            'wali_kelas_id' => 'nullable|exists:guru,id',
        ]);

        $oldData = DB::table('kelas_master')->where('id', $id)->first();

        $data = [
            'nama_kelas' => $request->nama_kelas,
            'wali_kelas_id' => $request->wali_kelas_id,
            'updated_at' => now(),
        ];

        DB::table('kelas_master')->where('id', $id)->update($data);

        $this->logActivity(
            'update',
            'kelas',
            "Mengupdate data kelas: {$request->nama_kelas}",
            $oldData,
            $data
        );

        return redirect()->route('data-master.kelas')
            ->with('success', 'Kelas berhasil diupdate!');
    }

    public function destroy($id)
    {
        $kelas = DB::table('kelas_master')->where('id', $id)->first();
        
        if (!$kelas) {
            return redirect()->route('data-master.kelas')
                ->with('error', 'Kelas tidak ditemukan!');
        }

        $namaKelas = $kelas->nama_kelas;
        DB::table('kelas_master')->where('id', $id)->delete();

        $this->logActivity(
            'delete',
            'kelas',
            "Menghapus data kelas: {$namaKelas}",
            $kelas,
            null
        );

        return redirect()->route('data-master.kelas')
            ->with('success', 'Kelas berhasil dihapus!');
    }
}