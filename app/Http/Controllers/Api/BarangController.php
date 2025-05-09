<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function index()
    {
        $barang = Barang::with(['satuan', 'pengguna'])->get();
        return response()->json($barang);
    }

    public function simpan(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:100',
            'id_satuan' => 'required|exists:satuan,id_satuan',
            'kode_barang' => 'required|string|max:50|unique:barang,kode_barang',
            'id_pengguna' => 'required|exists:pengguna,id',
            'stok' => 'required|integer|min:0',
        ]);

        $barang = Barang::create($request->all());

        return response()->json([
            'message' => 'Barang berhasil ditambahkan',
            'data' => $barang
        ], 201);
    }

    public function tampil($id)
    {
        $barang = Barang::with(['satuan', 'pengguna'])->find($id);

        if (!$barang) {
            return response()->json(['message' => 'Barang tidak ditemukan'], 404);
        }

        return response()->json($barang);
    }

    public function update(Request $request, $id)
    {
        $barang = Barang::find($id);

        if (!$barang) {
            return response()->json(['message' => 'Barang tidak ditemukan'], 404);
        }

        $request->validate([
            'nama_barang' => 'required|string|max:100',
            'id_satuan' => 'required|exists:satuan,id_satuan',
            'kode_barang' => 'required|string|max:50|unique:barang,kode_barang,' . $id . ',id_barang',
            'id_pengguna' => 'required|exists:pengguna,id',
            'stok' => 'required|integer|min:0',
        ]);

        $barang->update($request->all());

        return response()->json([
            'message' => 'Barang berhasil diperbarui',
            'data' => $barang
        ]);
    }

    public function delete($id)
    {
        $barang = Barang::find($id);

        if (!$barang) {
            return response()->json(['message' => 'Barang tidak ditemukan'], 404);
        }

        $barang->delete();

        return response()->json(['message' => 'Barang berhasil dihapus']);
    }
}
