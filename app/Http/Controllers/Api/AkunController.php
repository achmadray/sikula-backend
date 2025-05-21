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

   public function store(Request $request)
{
    $request->validate([
        'username' => 'required|unique:akun,username',
        'password' => 'required|min:6',
        'level' => 'required|in:admin,pegawai,kasir,pengelola_gudang',
        'nama_pengguna' => 'required|string',
        'email' => 'required|email',
        'no_telpon' => 'required|string',
    ]);

    $akun = Akun::create([
        'username' => $request->username,
        'password' => Hash::make($request->password),
        'level' => $request->level,
    ]);

    $pengguna = $akun->pengguna()->create([
        'nama_pengguna' => $request->nama_pengguna,
        'email' => $request->email,
        'no_telpon' => $request->no_telpon,
    ]);

    return response()->json([
        'message' => 'Akun dan Pengguna berhasil dibuat',
        'akun' => $akun,
        'pengguna' => $pengguna,
    ], 201);
}



    public function show($id)
    {
        $akun = Akun::with('pengguna')->find($id);

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

    try {
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
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
    }
}

    public function destroy($id)
    {
        $akun = Akun::find($id);
        if (!$akun) {
            return response()->json(['message' => 'Akun tidak ditemukan'], 404);
        }

        $akun->delete();
        return response()->json(['message' => 'Akun berhasil dihapus']);
    }
}
