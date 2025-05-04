<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AkunController extends Controller
{
    public function index()
    {
        return response()->json(Akun::all());
    }

    public function simpan(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:akun,username',
            'password' => 'required|min:6',
            'level' => 'required|in:admin,pegawai,kasir,pengelola_gudang',
        ]);

        $akun = Akun::tambah([
            'username' => $request->username,
            'password' => Hash::make($request->password), 
            'level' => $request->level,
        ]);

        return response()->json([
            'message' => 'Akun berhasil dibuat',
            'data' => $akun
        ], 201);
    }

    public function tampil($id)
    {
        $akun = Akun::find($id);
        if (!$akun) {
            return response()->json(['message' => 'Akun tidak ditemukan'], 404);
        }

        return response()->json($akun);
    }

    public function update(Request $request, $id)
    {
        $akun = Akun::find($id);
        if (!$akun) {
            return response()->json(['message' => 'Akun tidak ditemukan'], 404);
        }

        $request->validate([
            'username' => 'required|unique:akun,username,' . $id . ',id_akun',
            'password' => 'nullable|min:6',
            'level' => 'required|in:admin,pegawai,kasir,pengelola_gudang',
        ]);

        $akun->username = $request->username;
        if ($request->filled('password')) {
            $akun->password = Hash::make($request->password);
        }
        $akun->level = $request->level;
        $akun->save();

        return response()->json([
            'message' => 'Akun berhasil diperbarui',
            'data' => $akun
        ]);
    }

    public function delete($id)
    {
        $akun = Akun::find($id);
        if (!$akun) {
            return response()->json(['message' => 'Akun tidak ditemukan'], 404);
        }

        $akun->delete();
        return response()->json(['message' => 'Akun berhasil dihapus']);
    }
}
