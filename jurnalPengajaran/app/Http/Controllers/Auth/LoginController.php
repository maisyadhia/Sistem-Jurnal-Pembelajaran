<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Traits\LogsAdminActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
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

        // ============ 1. LOGIN ADMIN ============
        if ($request->role === 'admin') {
            $admin = Admin::where('username', $request->nik)
                         ->where('role', 'admin')
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

        // ============ 2. LOGIN GURU ============
        if ($request->role === 'guru') {
            $guru = Guru::where('nik', $request->nik)->first();

            if ($guru && (Hash::check($request->password, $guru->password) || password_verify($request->password, $guru->password))) {
                Session::flush();
                Session::put('guru_id', $guru->id);
                Session::put('guru_name', $guru->name ?? $guru->nama_guru);
                Session::put('user_role', 'guru');
                Session::put('user_name', $guru->name ?? $guru->nama_guru);
                Session::put('is_logged_in', true);
                Session::put('login_type', 'guru');

                return redirect()->route('guru.dashboard');
            }

            return back()->withErrors([
                'nik' => 'NIK atau Password Guru salah.',
            ]);
        }

        // ============ 3. LOGIN WALI MURID (PARENT) ============
        if ($request->role === 'parent') {
            $student = Student::where('nisn', $request->nik)->first();

            if ($student) {
                $dob = $student->dob ?? $student->birth_date ?? $student->tanggal_lahir ?? null;

                if ($dob) {
                    try {
                        // Bersihkan spasi & strip/slash
                        $cleanInput = preg_replace('/\D/', '', $request->password); // Ambil angka saja

                        // Jika input 8 digit angka (misal 25082010), ubah ke format Y-m-d
                        if (strlen($cleanInput) === 8) {
                            $formattedInputDob = Carbon::createFromFormat('dmY', $cleanInput)->format('Y-m-d');
                        } else {
                            $inputDobString = str_replace('/', '-', trim($request->password));
                            $formattedInputDob = Carbon::parse($inputDobString)->format('Y-m-d');
                        }

                        $formattedDbDob = Carbon::parse($dob)->format('Y-m-d');

                        if ($formattedInputDob === $formattedDbDob) {
                            Session::flush();
                            Session::put('student_id', $student->id);
                            Session::put('student_name', $student->name ?? $student->nama);
                            Session::put('user_role', 'parent');
                            Session::put('user_name', $student->parent_name ?? $student->nama_ortu ?? $student->name);
                            Session::put('is_logged_in', true);
                            Session::put('login_type', 'parent');

                            return redirect()->route('dashboard.timeline');
                        }
                    } catch (\Exception $e) {
                        // Jika parsing bermasalah
                    }
                }
            }

            return back()->withErrors([
                'nik' => 'NISN atau Tanggal Lahir Siswa salah. (Contoh ketik: 25082010)',
            ]);
        }

        return back()->withErrors([
            'nik' => 'Peran pengguna tidak valid.',
        ]);
    }

    public function logout(Request $request)
    {
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