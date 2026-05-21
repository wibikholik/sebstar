<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function updateProfile(Request $request)
    {
        // 1. Ambil data siswa terlogin via Token
        $user = $request->user(); 

        if (!$user) {
            return response()->json([
                'status'  => 'fail',
                'message' => 'Sesi login tidak valid atau kadaluarsa.'
            ], 401);
        }

        // 2. Validasi input dari React Native
        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'password' => 'nullable|string|min:6', // Diisi boleh, dikosongkan juga boleh
        ], [
            'name.required' => 'Nama lengkap wajib diisi.',
            'password.min'  => 'Password baru minimal harus 6 karakter.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 'fail',
                'message' => $validator->errors()->first()
            ], 422);
        }

        // 3. Eksekusi perubahan nama
        $user->name = $request->name;

        // 4. Eksekusi perubahan password (hanya jika siswa mengetik sesuatu di kolom password)
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // 5. Simpan ke database Laragon
        $user->save();

        // 6. Respon balik ke aplikasi Expo
        return response()->json([
            'status'  => 'success',
            'message' => 'Profil berhasil diperbarui.',
            'user'    => $user
        ], 200);
    }
}