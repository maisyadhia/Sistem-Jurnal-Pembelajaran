<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Admin;
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

        if ($request->role === 'parent') {
            // Login sebagai Wali Murid menggunakan NISN
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
                
                return redirect()->route('dashboard.timeline');
            }

            return back()->withErrors([
                'nik' => 'NISN atau Tanggal Lahir tidak ditemukan.',
            ]);
        }

        // Login sebagai Admin/Guru menggunakan NIK
        $admin = Admin::where('nik', $request->nik)
                      ->where('role', $request->role)
                      ->first();

        if ($admin && password_verify($request->password, $admin->password)) {
            Session::flush();
            Session::put('admin_id', $admin->id);
            Session::put('admin_name', $admin->name);
            Session::put('admin_role', $admin->role);
            Session::put('user_role', $admin->role);
            Session::put('user_name', $admin->name);
            Session::put('is_logged_in', true);

            if ($admin->role === 'admin' || $admin->role === 'humas') {
                return redirect()->route('monitoring');
            }

            if ($admin->role === 'guru') {
                return redirect()->route('jurnal');
            }
        }

        return back()->withErrors([
            'nik' => 'NIK atau Password salah.',
        ]);
    }

    public function logout(Request $request)
    {
        Session::flush();
        return redirect()->route('login');
    }
}