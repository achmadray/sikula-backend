<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Detail_Transaksi;
use Illuminate\Http\Request;

class DetailTransaksiController extends Controller
{
    public function index()
    {
        $detail_transaksi = Detail_Transaksi::all();
        return response()->json($detail_transaksi);
    }

    public function simpan(Request $request)
    {
        $request->validate([
            'id_menu' => 'required|exists:menu,id_menu',
            'id_transaksi' => 'required|exists:transaksi,id_transaksi',
            'jumlah' => 'required|integer',
            'total_harga' => 'required|numeric',
        ]);

        $detailTransaksi = Detail_Transaksi::tambah($request->all());
        return response()->json($detailTransaksi, 201);
    }

    public function tampil($id)
    {
        $detailTransaksi = Detail_Transaksi::find($id);

        if (!$detailTransaksi) {
            return response()->json(['message' => 'Detail Transaksi not found'], 404);
        }

        return response()->json($detailTransaksi);
    }

    public function update(Request $request, $id)
    {
        $detailTransaksi = Detail_Transaksi::find($id);

        if (!$detailTransaksi) {
            return response()->json(['message' => 'Detail Transaksi not found'], 404);
        }

        $detailTransaksi->update($request->all());
        return response()->json($detailTransaksi);
    }

    public function delete($id)
    {
        $detailTransaksi = Detail_Transaksi::find($id);

        if (!$detailTransaksi) {
            return response()->json(['message' => 'Detail Transaksi not found'], 404);
        }

        $detailTransaksi->delete();
        return response()->json(['message' => 'Detail Transaksi deleted successfully']);
    }
}
