<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use Illuminate\Http\Request;

class PenggunaController extends Controller
{
    public function index()
    {
        return response()->json(Pengguna::with('akun')->get());
    }

//     public function store(Request $request)
// {
//     try {
//         $request->validate([
//             'nama_pengguna' => 'required|string|max:100',
//             'email' => 'required|email|unique:pengguna,email',
//             'id_akun' => 'required|exists:akun,id_akun',
//             'no_telpon' => 'nullable|string|max:20',
//         ]);

//         $pengguna = Pengguna::create($request->all());

//         return response()->json([
//             'message' => 'Pengguna berhasil ditambahkan',
//             'data' => $pengguna
//         ], 201);

//     } catch (\Exception $e) {
//         return response()->json([
//             'message' => 'Terjadi kesalahan: ' . $e->getMessage()
//         ], 500);
//     }
// }

public function profil($id_akun)
{
    $pengguna = Pengguna::with('akun')->where('id_akun', $id_akun)->first();

    if (!$pengguna) {
        return response()->json(['message' => 'Pengguna tidak ditemukan'], 404);
    }

    return response()->json(['data' => $pengguna], 200);
}

    public function show($id)
    {
        $pengguna = Pengguna::with('akun')->find($id);

        if (!$pengguna) {
            return response()->json(['message' => 'Pengguna tidak ditemukan'], 404);
        }

        return response()->json($pengguna);
    }

    public function update(Request $request, $id)
{
    try {
        $pengguna = Pengguna::find($id);

        if (!$pengguna) {
            return response()->json(['message' => 'Pengguna tidak ditemukan'], 404);
        }

        $request->validate([
    'nama_pengguna' => 'required|string|max:100',
    'email' => 'required|email|unique:pengguna,email,' . $id . ',id_pengguna',
    'id_akun' => 'required|exists:akun,id_akun',
    'no_telpon' => 'nullable|string|max:20',
]);

        $pengguna->nama_pengguna = $request->nama_pengguna;
        $pengguna->email = $request->email;
        $pengguna->id_akun = $request->id_akun;
        $pengguna->no_telpon = $request->no_telpon;
        $pengguna->save();

        return response()->json([
            'message' => 'Pengguna berhasil diperbarui',
            'data' => $pengguna
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}


    public function delete($id)
{
    try {
        $pengguna = Pengguna::find($id);

        if (!$pengguna) {
            return response()->json(['message' => 'Pengguna tidak ditemukan'], 404);
        }

        $pengguna->delete();

        return response()->json(['message' => 'Pengguna berhasil dihapus']);

    } catch (\Exception $e) {
        return response()->json([
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}
}
