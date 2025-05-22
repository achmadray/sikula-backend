<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Detail_Transaksi;
use App\Models\Menu;
use Illuminate\Http\Request;

class DetailTransaksiController extends Controller
{
    public function index()
    {
        try {
            $detailTransaksi = Detail_Transaksi::with(['menu', 'transaksi'])->get();
            return response()->json($detailTransaksi);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
       $request->validate([
    'id_transaksi' => 'required|integer|exists:transaksi,id_transaksi',
    'id_menu' => 'required|integer|exists:menu,id_menu',
    'jumlah' => 'required|integer|min:1',
    'total_harga' => 'required|numeric|min:0',
]);

        $menu = Menu::findOrFail($request->id_menu);
        $totalHarga = $menu->harga * $request->jumlah;

        $detailTransaksi = Detail_Transaksi::create([
            'id_menu' => $request->id_menu,
            'id_transaksi' => $request->id_transaksi,
            'jumlah' => $request->jumlah,
            'total_harga' => $totalHarga,
        ]);

        return response()->json($detailTransaksi, 201);
    }

    public function show($id)
    {
        $detailTransaksi = Detail_Transaksi::with(['menu', 'transaksi'])->find($id);

        if (!$detailTransaksi) {
            return response()->json(['message' => 'Detail Transaksi tidak ditemukan'], 404);
        }

        return response()->json($detailTransaksi);
    }

    public function update(Request $request, $id)
    {
        $detailTransaksi = Detail_Transaksi::find($id);

        if (!$detailTransaksi) {
            return response()->json(['message' => 'Detail Transaksi tidak ditemukan'], 404);
        }

        $request->validate([
            'id_menu' => 'sometimes|exists:menu,id_menu',
            'id_transaksi' => 'sometimes|exists:transaksi,id_transaksi',
            'jumlah' => 'sometimes|integer|min:1',
        ]);

        $detailTransaksi->fill($request->only(['id_menu', 'id_transaksi', 'jumlah']));

        if ($request->has('id_menu') || $request->has('jumlah')) {
            $menu = Menu::find($detailTransaksi->id_menu);
            $detailTransaksi->total_harga = $menu->harga * $detailTransaksi->jumlah;
        }

        $detailTransaksi->save();

        return response()->json($detailTransaksi);
    }

    public function destroy($id)
    {
        $detailTransaksi = Detail_Transaksi::find($id);

        if (!$detailTransaksi) {
            return response()->json(['message' => 'Detail Transaksi tidak ditemukan'], 404);
        }

        $detailTransaksi->delete();

        return response()->json(['message' => 'Detail Transaksi berhasil dihapus']);
    }
}
