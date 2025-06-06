<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Akun;
use App\Models\Pengguna;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'username' => 'required|unique:akun,username',
                'password' => 'required',
                'level' => 'required',
            ]);

            $user = Akun::create([
                'username' => $validated['username'],
                'password' => bcrypt($validated['password']),
                'level' => $validated['level'],
            ]);

            return response()->json([
                'message' => 'Register berhasil',
                'user' => $user
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validasi gagal',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Terjadi kesalahan saat registrasi',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

   public function login(Request $request)
{
    $request->validate([
        'username' => 'required',
        'password' => 'required',
    ]);

    $credentials = $request->only('username', 'password');

    if (!$token = auth('api')->attempt($credentials)) {
        return response()->json(['error' => 'Username atau password salah'], 401);
    }

    $user = auth('api')->user();

    // Ambil ID pengguna dari relasi
    // $id_pengguna = optional($user->pengguna)->id;
    $pengguna = Pengguna::where('id_akun', $user->id_akun)->first();

    return response()->json([
        'token' => $token,
        'creds' => [
            'id' => $user->id_akun,
            'username' => $user->username,
            'level' => $user->level,
            'id_pengguna' => $pengguna->id_pengguna,
        ],
    ]);
}

    public function me(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found'
                ], 404);
            }

            return response()->json([
                'status' => 'success',
                'creds' => $user
            ]);
        } catch (JWTException $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Token is invalid or expired',
            ], 401);
        }
    }

    public function profile()
    {
        return response()->json(auth('api')->user());
    }

    public function logout()
    {
        auth('api')->logout();
        return response()->json(['message' => 'Berhasil logout']);
    }

  public function refresh()
{
    try {
        $newToken = JWTAuth::parseToken()->refresh();

        return response()->json([
            'access_token' => $newToken,
            'token_type' => 'bearer',
           'expires_in' => JWTAuth::factory()->getTTL() * 60,

        ]);
    } catch (\Tymon\JWTAuth\Exceptions\JWTException $e) {
        return response()->json(['error' => 'Token tidak valid atau sudah expired'], 401);
    }
}
}
