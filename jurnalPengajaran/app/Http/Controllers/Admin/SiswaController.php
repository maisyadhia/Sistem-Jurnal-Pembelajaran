<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SiswaController extends Controller
{
    use LogsAdminActivity;

    public function index()
    {
        $siswa = DB::table('students')->orderBy('class')->orderBy('name')->get();
        return view('admin.data-master.siswa', compact('siswa'));
    }

    public function create()
    {
        // Ambil daftar kelas untuk dropdown
        $kelas = DB::table('kelas_master')->orderBy('nama_kelas')->get();
        return view('admin.data-master.siswa-create', compact('kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nisn' => 'required|unique:students,nisn|min:10|max:10',
            'name' => 'required|string|max:255',
            'dob' => 'required|date',
            'class' => 'required|string',
            'parent_name' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:15',
        ]);

        $data = [
            'nisn' => $request->nisn,
            'name' => $request->name,
            'dob' => $request->dob,
            'class' => $request->class,
            'parent_name' => $request->parent_name,
            'parent_phone' => $request->parent_phone,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('students')->insert($data);

        $this->logActivity(
            'create',
            'siswa',
            "Menambahkan siswa baru: {$request->name} (NISN: {$request->nisn})",
            null,
            $data
        );

        return redirect()->route('data-master.siswa')
            ->with('success', 'Siswa berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $siswa = DB::table('students')->where('id', $id)->first();
        if (!$siswa) {
            return redirect()->route('data-master.siswa')
                ->with('error', 'Siswa tidak ditemukan!');
        }
        $kelas = DB::table('kelas_master')->orderBy('nama_kelas')->get();
        return view('admin.data-master.siswa-edit', compact('siswa', 'kelas'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'nisn' => 'required|min:10|max:10|unique:students,nisn,' . $id,
            'name' => 'required|string|max:255',
            'dob' => 'required|date',
            'class' => 'required|string',
            'parent_name' => 'nullable|string|max:255',
            'parent_phone' => 'nullable|string|max:15',
        ]);

        $oldData = DB::table('students')->where('id', $id)->first();

        $data = [
            'nisn' => $request->nisn,
            'name' => $request->name,
            'dob' => $request->dob,
            'class' => $request->class,
            'parent_name' => $request->parent_name,
            'parent_phone' => $request->parent_phone,
            'updated_at' => now(),
        ];

        DB::table('students')->where('id', $id)->update($data);

        $this->logActivity(
            'update',
            'siswa',
            "Mengupdate data siswa: {$request->name} (NISN: {$request->nisn})",
            $oldData,
            $data
        );

        return redirect()->route('data-master.siswa')
            ->with('success', 'Siswa berhasil diupdate!');
    }

    public function destroy($id)
    {
        $siswa = DB::table('students')->where('id', $id)->first();
        
        DB::table('students')->where('id', $id)->delete();

        $this->logActivity(
            'delete',
            'siswa',
            "Menghapus data siswa: {$siswa->name} (NISN: {$siswa->nisn})",
            $siswa,
            null
        );

        return redirect()->route('data-master.siswa')
            ->with('success', 'Siswa berhasil dihapus!');
    }
}