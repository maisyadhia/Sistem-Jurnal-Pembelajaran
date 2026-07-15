<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MapelController extends Controller
{
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

        DB::table('mapel_master')->insert([
            'kode_mapel' => $request->kode_mapel,
            'nama_mapel' => $request->nama_mapel,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

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

        DB::table('mapel_master')->where('id', $id)->update([
            'kode_mapel' => $request->kode_mapel,
            'nama_mapel' => $request->nama_mapel,
            'updated_at' => now(),
        ]);

        return redirect()->route('data-master.mapel')
            ->with('success', 'Mata Pelajaran berhasil diupdate!');
    }

    public function destroy($id)
    {
        DB::table('mapel_master')->where('id', $id)->delete();
        return redirect()->route('data-master.mapel')
            ->with('success', 'Mata Pelajaran berhasil dihapus!');
    }
}