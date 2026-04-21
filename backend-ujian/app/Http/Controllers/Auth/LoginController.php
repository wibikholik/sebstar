<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // 1. Menampilkan halaman login
    public function index() {
        return view('auth.login');
    }

    // 2. Menangani proses login
    public function login(Request $request) {
        // Validasi inputan user
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Cek kecocokan di database
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Ambil data user yang berhasil login
            $user = Auth::user();

            // Cek role dan arahkan ke dashboard yang benar
            if ($user->role == 'admin') {
                return redirect()->intended('/admin/dashboard');
            } elseif ($user->role == 'guru') {
                return redirect()->intended('/guru/dashboard');
            } elseif ($user->role == 'pengawas') {
                return redirect()->intended('/pengawas/dashboard');
            }

            // Jika siswa nyasar ke web login
            Auth::logout();
            return back()->with('error', 'Siswa login lewat aplikasi mobile!');
        }

        // Jika salah email/password
        return back()->withErrors(['email' => 'Email atau password salah!']);
    }

    // 3. Menangani Logout
    public function logout(Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}