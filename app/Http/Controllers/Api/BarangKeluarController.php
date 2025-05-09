<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang_Keluar;
use Illuminate\Http\Request;

class BarangKeluarController extends Controller
{
    public function index()
    {
        $barang_Keluar = Barang_Keluar::all();
        return response()->json($barang_Keluar);
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_barang' => 'required|exists:barang,id_barang',
            'id_pengguna' => 'required|exists:pengguna,id_pengguna',
            'tanggal_keluar' => 'required|date',
            'jumlah' => 'required|integer',
            'stok_keluar' => 'required|integer',
            'catatan' => 'nullable|string',
        ]);

        $barangKeluar = Barang_Keluar::create($request->all());
        return response()->json($barangKeluar, 201);
    }

    public function show($id)
    {
        $barangKeluar = Barang_Keluar::find($id);

        if (!$barangKeluar) {
            return response()->json(['message' => 'Barang Keluar not found'], 404);
        }

        return response()->json($barangKeluar);
    }

    public function update(Request $request, $id)
    {
        $barangKeluar = Barang_Keluar::find($id);

        if (!$barangKeluar) {
            return response()->json(['message' => 'Barang Keluar not found'], 404);
        }

        $barangKeluar->update($request->all());
        return response()->json($barangKeluar);
    }

    public function destroy($id)
    {
        $barangKeluar = Barang_Keluar::find($id);

        if (!$barangKeluar) {
            return response()->json(['message' => 'Barang Keluar not found'], 404);
        }

        $barangKeluar->delete();
        return response()->json(['message' => 'Barang Keluar deleted successfully']);
    }
}
