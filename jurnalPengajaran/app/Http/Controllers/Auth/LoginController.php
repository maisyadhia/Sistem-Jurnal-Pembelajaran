<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
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
            'nisn' => 'required|string',
            'dob' => 'required',
        ]);

        // Check if it's admin login
        if ($request->nisn === 'admin' && $request->dob === 'admin123') {
            Session::put('user_role', 'admin');
            Session::put('user_name', 'Administrator');
            return redirect()->route('monitoring');
        }

        // Check if it's teacher login
        if ($request->nisn === 'guru' && $request->dob === 'guru123') {
            Session::put('user_role', 'teacher');
            Session::put('user_name', 'Ahmad Subardjo');
            Session::put('teacher_id', 1);
            return redirect()->route('jurnal');
        }

        // Find student by NISN and date of birth
        $student = Student::where('nisn', $request->nisn)
                          ->where('dob', $request->dob)
                          ->first();

        if ($student) {
            Session::put('student_id', $student->id);
            Session::put('student_name', $student->name);
            Session::put('student_class', $student->class);
            Session::put('user_role', 'parent');
            Session::put('user_name', $student->parent_name ?? 'Wali Murid');
            
            return redirect()->route('dashboard.timeline');
        }

        return back()->withErrors([
            'nisn' => 'NISN atau Tanggal Lahir tidak ditemukan.',
        ])->withInput();
    }

    public function logout(Request $request)
    {
        Session::flush();
        return redirect()->route('login');
    }
}