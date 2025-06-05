<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Akun;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
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

    } catch (\Illuminate\Validation\ValidationException $e) {
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
        $credentials = $request->only('username', 'password');

        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Username atau password salah'], 401);
        }

        return response()->json([
            'token' => $token,
            'user' => auth()->user()
        ]);
    }

    public function profile()
    {
        return response()->json(auth()->user());
    }

    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Berhasil logout']);
    }
}

