<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KelasController extends Controller
{
    public function index()
    {
        $kelas = DB::table('kelas_master')->orderBy('nama_kelas')->get();
        return view('admin.data-master.kelas', compact('kelas'));
    }

    public function create()
    {
        return view('admin.data-master.kelas-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_kelas' => 'required|unique:kelas_master,nama_kelas',
            'wali_kelas' => 'required',
        ]);

        DB::table('kelas_master')->insert([
            'nama_kelas' => $request->nama_kelas,
            'wali_kelas' => $request->wali_kelas,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

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
        return view('admin.data-master.kelas-edit', compact('kelas'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kelas' => 'required|unique:kelas_master,nama_kelas,' . $id,
            'wali_kelas' => 'required',
        ]);

        DB::table('kelas_master')->where('id', $id)->update([
            'nama_kelas' => $request->nama_kelas,
            'wali_kelas' => $request->wali_kelas,
            'updated_at' => now(),
        ]);

        return redirect()->route('data-master.kelas')
            ->with('success', 'Kelas berhasil diupdate!');
    }

    public function destroy($id)
    {
        DB::table('kelas_master')->where('id', $id)->delete();
        return redirect()->route('data-master.kelas')
            ->with('success', 'Kelas berhasil dihapus!');
    }
}