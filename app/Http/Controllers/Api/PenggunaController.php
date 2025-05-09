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

    public function store(Request $request)
    {
        $request->validate([
            'nama_pengguna' => 'required|string|max:100',
            'email' => 'required|email|unique:pengguna,email',
            'id_akun' => 'required|exists:akun,id',
            'no_telpon' => 'nullable|string|max:20',
        ]);

        try {
            $pengguna = Pengguna::create($request->all());

            return response()->json([
                'message' => 'Pengguna berhasil ditambahkan',
                'data' => $pengguna
            ], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
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
        $pengguna = Pengguna::find($id);

        if (!$pengguna) {
            return response()->json(['message' => 'Pengguna tidak ditemukan'], 404);
        }

        try {
            $request->validate([
                'nama_pengguna' => 'required|string|max:100',
                'email' => 'required|email|unique:pengguna,email,' . $id . ',id_pengguna',
                'id_akun' => 'required|exists:akun,id',
                'no_telpon' => 'nullable|string|max:20',
            ]);

            $pengguna->update($request->all());

            return response()->json([
                'message' => 'Pengguna berhasil diperbarui',
                'data' => $pengguna
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        $pengguna = Pengguna::find($id);

        if (!$pengguna) {
            return response()->json(['message' => 'Pengguna tidak ditemukan'], 404);
        }

        try {
            $pengguna->delete();

            return response()->json(['message' => 'Pengguna berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }
}
