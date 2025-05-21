<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function stokSummary()
    {
        $totalStokMasuk = DB::table('barang_masuk')->sum('stok_masuk');
        $totalStokKeluar = DB::table('barang_keluar')->sum('stok_keluar');

        return response()->json([
            'total_stok_masuk' => $totalStokMasuk ?? 0,
            'total_stok_keluar' => $totalStokKeluar ?? 0,
        ]);
    }
}
