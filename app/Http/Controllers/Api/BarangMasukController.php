<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang_Masuk;
use Illuminate\Http\Request;

class BarangMasukController extends Controller
{
    public function index()
    {
        $barang_masuk = Barang_Masuk::all();
        return response()->json($barang_masuk);
    }

    public function simpan(Request $request)
    {
        $request->validate([
            'id_suplier' => 'required|exists:suplier,id_suplier',
            'id_barang' => 'required|exists:barang,id_barang',
            'harga' => 'required|numeric',
            'jumlah' => 'required|integer',
            'total_harga' => 'required|numeric',
            'stok_masuk' => 'required|integer',
            'tanggal_masuk' => 'required|date',
        ]);

        $barangMasuk = Barang_Masuk::create($request->all());
        return response()->json($barangMasuk, 201);
    }

    public function tampil($id)
    {
        $barangMasuk = Barang_Masuk::find($id);

        if (!$barangMasuk) {
            return response()->json(['message' => 'Barang Masuk not found'], 404);
        }

        return response()->json($barangMasuk);
    }

    public function update(Request $request, $id)
    {
        $barangMasuk = Barang_Masuk::find($id);

        if (!$barangMasuk) {
            return response()->json(['message' => 'Barang Masuk not found'], 404);
        }

        $barangMasuk->update($request->all());
        return response()->json($barangMasuk);
    }

    public function delete($id)
    {
        $barangMasuk = Barang_Masuk::find($id);

        if (!$barangMasuk) {
            return response()->json(['message' => 'Barang Masuk not found'], 404);
        }

        $barangMasuk->delete();
        return response()->json(['message' => 'Barang Masuk deleted successfully']);
    }
}
