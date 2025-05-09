<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pengguna;
use Illuminate\Http\Request;

class PenggunaController extends Controller
{
    public function index()
    {
        // Menampilkan semua pengguna dengan relasi akun
        return response()->json(Pengguna::with('akun')->get());
    }

    public function simpan(Request $request)
    {
        // Validasi data yang diterima
        $request->validate([
            'nama_pengguna' => 'required|string|max:100',
            'email' => 'required|email|unique:pengguna,email',
            'id_akun' => 'required|exists:akun,id',
            'no_telpon' => 'nullable|string|max:20',
        ]);

        // Membuat data pengguna baru
        $pengguna = Pengguna::create($request->all());

        // Mengembalikan response sukses
        return response()->json([
            'message' => 'Pengguna berhasil ditambahkan',
            'data' => $pengguna
        ], 201);
    }

    public function tampil($id)
    {
        // Menampilkan pengguna berdasarkan ID dengan relasi akun
        $pengguna = Pengguna::with('akun')->find($id);

        // Jika pengguna tidak ditemukan
        if (!$pengguna) {
            return response()->json(['message' => 'Pengguna tidak ditemukan'], 404);
        }

        return response()->json($pengguna);
    }

    public function update(Request $request, $id)
    {
        // Mencari pengguna berdasarkan ID
        $pengguna = Pengguna::find($id);

        // Jika pengguna tidak ditemukan, kembalikan pesan error
        if (!$pengguna) {
            return response()->json(['message' => 'Pengguna tidak ditemukan'], 404);
        }

        // Validasi data yang diterima dari request
        $request->validate([
            'nama_pengguna' => 'required|string|max:100',
            'email' => 'required|email|unique:pengguna,email,' . $id . ',id_pengguna',
            'id_akun' => 'required|exists:akun,id',
            'no_telpon' => 'nullable|string|max:20',
        ]);

        // Update data pengguna
        $pengguna->nama_pengguna = $request->nama_pengguna;
        $pengguna->email = $request->email;
        $pengguna->id_akun = $request->id_akun;
        $pengguna->no_telpon = $request->no_telpon;

        // Simpan perubahan
        $pengguna->save();

        // Kembalikan respon sukses dengan data yang telah diperbarui
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
