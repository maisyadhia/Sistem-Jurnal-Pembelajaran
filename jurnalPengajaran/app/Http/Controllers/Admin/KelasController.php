<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KelasController extends Controller
{
    use LogsAdminActivity;

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

        $data = [
            'nama_kelas' => $request->nama_kelas,
            'wali_kelas' => $request->wali_kelas,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('kelas_master')->insert($data);

        $this->logActivity(
            'create',
            'kelas',
            "Menambahkan kelas baru: {$request->nama_kelas} (Wali: {$request->wali_kelas})",
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
        return view('admin.data-master.kelas-edit', compact('kelas'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nama_kelas' => 'required|unique:kelas_master,nama_kelas,' . $id,
            'wali_kelas' => 'required',
        ]);

        $oldData = DB::table('kelas_master')->where('id', $id)->first();

        $data = [
            'nama_kelas' => $request->nama_kelas,
            'wali_kelas' => $request->wali_kelas,
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
        
        DB::table('kelas_master')->where('id', $id)->delete();

        $this->logActivity(
            'delete',
            'kelas',
            "Menghapus data kelas: {$kelas->nama_kelas}",
            $kelas,
            null
        );

        return redirect()->route('data-master.kelas')
            ->with('success', 'Kelas berhasil dihapus!');
    }
}