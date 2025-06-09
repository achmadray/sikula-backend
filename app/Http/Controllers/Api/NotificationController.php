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
        $stokHampirHabis = Barang::with('satuan')
            ->where('stok', '<=', 5)
            ->get();

        $barangMasukTerbaru = Barang_Masuk::with('barang.satuan')
            ->orderByDesc('tanggal_masuk')
            ->limit(5)
            ->get();

        $barangKeluarTerbaru = Barang_Keluar::with('barang.satuan')
            ->orderByDesc('tanggal_keluar')
            ->limit(5)
            ->get();

        return response()->json([
            'stok_hampir_habis' => $stokHampirHabis,
            'barang_masuk_terbaru' => $barangMasukTerbaru,
            'barang_keluar_terbaru' => $barangKeluarTerbaru,
        ]);
    }
}
