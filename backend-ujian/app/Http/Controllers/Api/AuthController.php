<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'nis' => 'required',
            'password' => 'required',
        ]);

        $user = User::with('classroom')->where('nis', $request->nis)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'NIS atau password salah'], 401);
        }

        // --- PROTEKSI DOUBLE LOGIN ---
        if ($user->is_logged_in == 1) {
            return response()->json([
                'message' => 'Akun Anda sedang aktif di perangkat lain. Silahkan hubungi pengawas.'
            ], 403);
        }

        // Ubah status jadi sedang login
        $user->update(['is_logged_in' => 1]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'user' => $user
        ]);
    }

    public function me(Request $request)
    {
        $user = User::with('classroom')->find(Auth::id());
        return response()->json(['user' => $user]);
    }

    public function logout(Request $request)
    {
        try {
            $user = $request->user();
            if ($user) {
                // Kembalikan status perangkat menjadi 0 agar bisa login lagi
                $user->update(['is_logged_in' => 0]);
                $user->currentAccessToken()->delete();
            }

            return response()->json(['message' => 'Berhasil keluar, device di-reset.'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Gagal logout dari server'], 500);
        }
    }
}