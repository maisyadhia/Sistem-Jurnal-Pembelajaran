<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class ProfileController extends Controller
{
    public function index()
    {
        // Mengambil ID admin dari session (sesuai yang dibuat teman Anda saat login)
        $adminId = Session::get('admin_id');
        
        // Ambil data admin dari database
        $admin = DB::table('admins')->where('id', $adminId)->first();
        
        if (!$admin) {
            return redirect()->route('login')->with('error', 'Sesi berakhir, silakan login kembali.');
        }

        return view('admin.profile', compact('admin'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password_lama' => 'required',
            'password_baru' => 'required|min:6|confirmed', // 'confirmed' butuh input bernama password_baru_confirmation
        ], [
            'password_baru.confirmed' => 'Konfirmasi password baru tidak cocok.',
            'password_baru.min' => 'Password baru minimal 6 karakter.',
        ]);

        $adminId = Session::get('admin_id');
        $admin = DB::table('admins')->where('id', $adminId)->first();

        // Cek apakah password lama yang dimasukkan benar
        if (!Hash::check($request->password_lama, $admin->password)) {
            return back()->with('error', 'Password lama yang Anda masukkan salah!');
        }

        // Simpan password baru (di-hash)
        DB::table('admins')->where('id', $adminId)->update([
            'password' => Hash::make($request->password_baru),
            'updated_at' => now()
        ]);

        // Log aktivitas (opsional, jika Anda punya trait LogsAdminActivity)
        // $this->logActivity('update', 'admin', "Admin {$admin->name} mengganti password");

        return back()->with('success', 'Password berhasil diperbarui!');
    }
}