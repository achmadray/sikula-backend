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
    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:100',
            'id_satuan' => 'required|exists:satuan,id_satuan',
            'kode_barang' => 'required|string|max:50|unique:barang,kode_barang',
            'id_pengguna' => 'required|exists:pengguna,id_pengguna',
            'stok' => 'required|integer|min:0',
        ]);

        $barang = Barang::create($request->all());

        return response()->json([
            'message' => 'Barang berhasil ditambahkan',
            'data' => $barang
        ], 201);
    }

    public function show($id)
    {
         $barang = Barang::with(['satuan', 'pengguna'])->findOrFail($id);

        return response()->json($barang);
    }

    public function update(Request $request, $id)
    {
        $barang = Barang::findOrFail($id);

        $request->validate([
            'nama_barang' => 'required|string|max:100',
            'id_satuan' => 'required|exists:satuan,id_satuan',
            'kode_barang' => 'required|string|max:50|unique:barang,kode_barang,' . $id . ',id_barang',
            'id_pengguna' => 'required|exists:pengguna,id_pengguna',
            'stok' => 'required|integer|min:0',
        ]);

        $barang->update($request->all());

        return response()->json([
            'message' => 'Barang berhasil diperbarui',
            'data' => $barang
        ]);
    }

    public function destroy($id)
    {
        $barang = Barang::findOrFail($id);

        $barang->delete();

        return response()->json(['message' => 'Barang berhasil dihapus']);
    }
}
