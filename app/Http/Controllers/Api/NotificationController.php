<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\Barang_Masuk;
use App\Models\Barang_Keluar;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index()
    {
        $batasStok = 5;

        $stok_hampir_habis = Barang::with('satuan')
            ->where('stok', '<=', $batasStok)
            ->get();

        $barang_masuk_terbaru = Barang_Masuk::with('barang')
            ->orderBy('tanggal_masuk', 'desc')
            ->limit(5)
            ->get();

        $barang_keluar_terbaru = Barang_Keluar::with('barang')
            ->orderBy('tanggal_keluar', 'desc')
            ->limit(5)
            ->get();

        return response()->json([
            'stok_hampir_habis' => $stok_hampir_habis,
            'barang_masuk_terbaru' => $barang_masuk_terbaru,
            'barang_keluar_terbaru' => $barang_keluar_terbaru,
        ]);
    }
}
