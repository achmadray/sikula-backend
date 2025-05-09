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

    public function simpan(Request $request)
    {
        $request->validate([
            'nama_pengguna' => 'required|string|max:100',
            'email' => 'required|email|unique:pengguna,email',
            'id_akun' => 'required|exists:akun,id',
            'no_telpon' => 'nullable|string|max:20',
        ]);

        $pengguna = Pengguna::create($request->all());

        return response()->json([
            'message' => 'Pengguna berhasil ditambahkan',
            'data' => $pengguna
        ], 201);
    }

    public function tampil($id)
    {
        $pengguna = Pengguna::with('akun')->find($id);

        if (!$pengguna) {
            return response()->json(['message' => 'Pengguna tidak ditemukan'], 404);
        }

        return response()->json($pengguna);
    }

    public function update(Request $request, $id)
    {
        $pengguna = Pengguna::find($id);

        if (!$pengguna) {
            return response()->json(['message' => 'Pengguna tidak ditemukan'], 404);
        }
        $request->validate([
            'nama_pengguna' => 'required|string|max:100',
            'email' => 'required|email|unique:pengguna,email,' . $id . ',id_pengguna',
            'id_akun' => 'required|exists:akun,id',
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
    }

    public function delete($id)
    {
        $pengguna = Pengguna::find($id);
        if (!$pengguna) {
            return response()->json(['message' => 'Pengguna tidak ditemukan'], 404);
        }

        $pengguna->delete();

        return response()->json(['message' => 'Pengguna berhasil dihapus']);
    }
}
