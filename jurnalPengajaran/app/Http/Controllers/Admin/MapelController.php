<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MapelController extends Controller
{
    use LogsAdminActivity;

    public function index()
    {
        $mapel = DB::table('mapel_master')->orderBy('nama_mapel')->get();
        return view('admin.data-master.mapel', compact('mapel'));
    }

    public function create()
    {
        return view('admin.data-master.mapel-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_mapel' => 'required|unique:mapel_master,kode_mapel',
            'nama_mapel' => 'required|unique:mapel_master,nama_mapel',
        ]);

        $data = [
            'kode_mapel' => $request->kode_mapel,
            'nama_mapel' => $request->nama_mapel,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('mapel_master')->insert($data);

        $this->logActivity(
            'create',
            'mapel',
            "Menambahkan mata pelajaran baru: {$request->nama_mapel} (Kode: {$request->kode_mapel})",
            null,
            $data
        );

        return redirect()->route('data-master.mapel')
            ->with('success', 'Mata Pelajaran berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $mapel = DB::table('mapel_master')->where('id', $id)->first();
        if (!$mapel) {
            return redirect()->route('data-master.mapel')
                ->with('error', 'Mata Pelajaran tidak ditemukan!');
        }
        return view('admin.data-master.mapel-edit', compact('mapel'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_mapel' => 'required|unique:mapel_master,kode_mapel,' . $id,
            'nama_mapel' => 'required|unique:mapel_master,nama_mapel,' . $id,
        ]);

        $oldData = DB::table('mapel_master')->where('id', $id)->first();

        $data = [
            'kode_mapel' => $request->kode_mapel,
            'nama_mapel' => $request->nama_mapel,
            'updated_at' => now(),
        ];

        DB::table('mapel_master')->where('id', $id)->update($data);

        $this->logActivity(
            'update',
            'mapel',
            "Mengupdate mata pelajaran: {$request->nama_mapel}",
            $oldData,
            $data
        );

        return redirect()->route('data-master.mapel')
            ->with('success', 'Mata Pelajaran berhasil diupdate!');
    }

    public function destroy($id)
    {
        $mapel = DB::table('mapel_master')->where('id', $id)->first();
        
        DB::table('mapel_master')->where('id', $id)->delete();

        $this->logActivity(
            'delete',
            'mapel',
            "Menghapus mata pelajaran: {$mapel->nama_mapel}",
            $mapel,
            null
        );

        return redirect()->route('data-master.mapel')
            ->with('success', 'Mata Pelajaran berhasil dihapus!');
    }
}