<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Admin;
use App\Models\Guru;
use App\Models\Student;

class LoginController extends Controller
{
    use LogsAdminActivity;

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
            // LOGIN ADMIN - HANYA CEK role 'admin' (humas dihapus)
            $admin = Admin::where('username', $request->nik)
                         ->where('role', 'admin') // HANYA admin
                         ->first();

            if ($admin && password_verify($request->password, $admin->password)) {
                Session::flush();
                Session::put('admin_id', $admin->id);
                Session::put('admin_name', $admin->name);
                Session::put('admin_role', 'admin');
                Session::put('user_role', 'admin');
                Session::put('user_name', $admin->name);
                Session::put('is_logged_in', true);
                Session::put('login_type', 'admin');

                // Log aktivitas login
                $this->logActivity(
                    'login',
                    'auth',
                    "Admin {$admin->name} login ke sistem",
                    null,
                    ['username' => $admin->username, 'role' => 'admin']
                );

                return redirect()->route('monitoring');
            }

            return back()->withErrors([
                'nik' => 'Username atau Password Admin salah.',
            ]);
        }
    }

    public function logout(Request $request)
    {
        // Log aktivitas logout
        if (Session::get('admin_id')) {
            $this->logActivity(
                'logout',
                'auth',
                "Admin " . Session::get('admin_name') . " logout dari sistem"
            );
        }

        Session::flush();
        return redirect()->route('login');
    }
}