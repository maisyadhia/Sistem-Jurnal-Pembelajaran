<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class GuruController extends Controller
{
    use LogsAdminActivity;

    public function index()
    {
        $guru = DB::table('guru')->orderBy('nama_guru')->get();
        return view('admin.data-master.guru', compact('guru'));
    }

    public function create()
    {
        return view('admin.data-master.guru-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode_guru' => 'required|unique:guru,kode_guru',
            'nik' => 'required|unique:guru,nik|min:16',
            'nama_guru' => 'required',
            'password' => 'required|min:6',
            'role' => 'required|in:guru,admin,humas',
        ]);

        $data = [
            'kode_guru' => $request->kode_guru,
            'nik' => $request->nik,
            'nama_guru' => $request->nama_guru,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'created_at' => now(),
            'updated_at' => now(),
        ];

        DB::table('guru')->insert($data);

        // Log aktivitas
        $this->logActivity(
            'create',
            'guru',
            "Menambahkan guru baru: {$request->nama_guru} (NIK: {$request->nik})",
            null,
            ['kode_guru' => $request->kode_guru, 'nik' => $request->nik, 'nama_guru' => $request->nama_guru, 'role' => $request->role]
        );

        return redirect()->route('data-master.guru')
            ->with('success', 'Guru berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $guru = DB::table('guru')->where('id', $id)->first();
        if (!$guru) {
            return redirect()->route('data-master.guru')
                ->with('error', 'Guru tidak ditemukan!');
        }
        return view('admin.data-master.guru-edit', compact('guru'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'kode_guru' => 'required|unique:guru,kode_guru,' . $id,
            'nik' => 'required|min:16|unique:guru,nik,' . $id,
            'nama_guru' => 'required',
            'role' => 'required|in:guru,admin,humas',
        ]);

        // Ambil data lama
        $oldData = DB::table('guru')->where('id', $id)->first();

        $data = [
            'kode_guru' => $request->kode_guru,
            'nik' => $request->nik,
            'nama_guru' => $request->nama_guru,
            'role' => $request->role,
            'updated_at' => now(),
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        DB::table('guru')->where('id', $id)->update($data);

        // Log aktivitas
        $this->logActivity(
            'update',
            'guru',
            "Mengupdate data guru: {$request->nama_guru} (NIK: {$request->nik})",
            ['kode_guru' => $oldData->kode_guru, 'nik' => $oldData->nik, 'nama_guru' => $oldData->nama_guru, 'role' => $oldData->role],
            ['kode_guru' => $request->kode_guru, 'nik' => $request->nik, 'nama_guru' => $request->nama_guru, 'role' => $request->role]
        );

        return redirect()->route('data-master.guru')
            ->with('success', 'Guru berhasil diupdate!');
    }

    public function destroy($id)
    {
        $guru = DB::table('guru')->where('id', $id)->first();
        
        DB::table('guru')->where('id', $id)->delete();

        // Log aktivitas
        $this->logActivity(
            'delete',
            'guru',
            "Menghapus data guru: {$guru->nama_guru} (NIK: {$guru->nik})",
            ['kode_guru' => $guru->kode_guru, 'nik' => $guru->nik, 'nama_guru' => $guru->nama_guru, 'role' => $guru->role],
            null
        );

        return redirect()->route('data-master.guru')
            ->with('success', 'Guru berhasil dihapus!');
    }
}