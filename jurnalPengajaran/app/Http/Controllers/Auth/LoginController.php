<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Admin;
use App\Models\Guru;
use App\Models\Student;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'nik' => 'required|string',
            'password' => 'required|string',
            'role' => 'required|in:admin,guru,parent',
        ]);

        if ($request->role === 'admin') {
            // LOGIN ADMIN menggunakan username (bukan NIK)
            $admin = Admin::where('username', $request->nik)
                         ->whereIn('role', ['admin', 'humas'])
                         ->first();

            if ($admin && password_verify($request->password, $admin->password)) {
                Session::flush();
                Session::put('admin_id', $admin->id);
                Session::put('admin_name', $admin->name);
                Session::put('admin_role', $admin->role);
                Session::put('user_role', $admin->role);
                Session::put('user_name', $admin->name);
                Session::put('is_logged_in', true);
                Session::put('login_type', 'admin');

                return redirect()->route('monitoring');
            }

            return back()->withErrors([
                'nik' => 'Username atau Password Admin salah.',
            ]);
        }

        if ($request->role === 'guru') {
            // LOGIN GURU menggunakan NIK
            // VALIDASI PANJANG NIK
            if (strlen($request->nik) < 16) {
                return back()->withErrors([
                    'nik' => 'NIK Guru harus minimal 16 digit!',
                ])->withInput();
            }

            $guru = Guru::where('nik', $request->nik)->first();

            if ($guru && password_verify($request->password, $guru->password)) {
                Session::flush();
                Session::put('guru_id', $guru->id);
                Session::put('guru_name', $guru->nama_guru);
                Session::put('user_role', 'guru');
                Session::put('user_name', $guru->nama_guru);
                Session::put('is_logged_in', true);
                Session::put('login_type', 'guru');

                return redirect()->route('guru.dashboard');
            }

            return back()->withErrors([
                'nik' => 'NIK atau Password Guru salah.',
            ]);
        }

        if ($request->role === 'parent') {
            // LOGIN PARENT menggunakan NISN
            $student = Student::where('nisn', $request->nik)
                              ->where('dob', $request->password)
                              ->first();

            if ($student) {
                Session::flush();
                Session::put('student_id', $student->id);
                Session::put('student_name', $student->name);
                Session::put('student_class', $student->class);
                Session::put('user_role', 'parent');
                Session::put('user_name', $student->parent_name ?? 'Wali Murid');
                Session::put('is_logged_in', true);
                Session::put('login_type', 'parent');
                
                return redirect()->route('dashboard.timeline');
            }

            return back()->withErrors([
                'nik' => 'NISN atau Tanggal Lahir tidak ditemukan.',
            ]);
        }

        return back()->withErrors([
            'nik' => 'Login gagal. Silakan coba lagi.',
        ]);
    }

    public function logout(Request $request)
    {
        Session::flush();
        return redirect()->route('login');
    }
}