<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Http\Request;

class TransaksiController extends Controller
{
    public function index()
    {
        $transaksi = Transaksi::all();
        return response()->json($transaksi);
    }

    public function simpan(Request $request)
    {
        $request->validate([
            'id_pengguna' => 'required|exists:pengguna,id_pengguna',
            'no_urut' => 'required|string|max:50|unique:transaksi',
            'nama_order' => 'required|string|max:255',
            'metode_pembayaran' => 'required|string|max:100',
            'total_transaksi' => 'required|numeric',
            'tanggal_transaksi' => 'required|date',
            'status_pembayaran' => 'required|in:pending,lunas,gagal',
        ]);

        $transaksi = Transaksi::create($request->all());
        return response()->json($transaksi, 201);
    }

    public function tampil($id)
    {
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi not found'], 404);
        }

        return response()->json($transaksi);
    }

    public function update(Request $request, $id)
    {
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi not found'], 404);
        }

        $transaksi->update($request->all());
        return response()->json($transaksi);
    }

    public function delete($id)
    {
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi not found'], 404);
        }

        $transaksi->delete();
        return response()->json(['message' => 'Transaksi deleted successfully']);
    }
}
