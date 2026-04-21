<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User; // Pastikan model User sudah dibuat

class AuthController extends Controller
{
    public function login(Request $request)
{
    $request->validate([
        'nis' => 'required',
        'password' => 'required',
    ]);

    // PASTIKAN INI MENCARI NIS, BUKAN EMAIL!
    $user = User::where('nis', $request->nis)->first();

    if (! $user || ! \Hash::check($request->password, $user->password)) {
        return response()->json(['message' => 'NIS atau password salah'], 401);
    }

    $token = $user->createToken('auth_token')->plainTextToken;

    return response()->json([
        'access_token' => $token,
        'user' => $user
    ]);

}
}
