<?php

namespace App\Http\Controllers\Api;

use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use App\Models\Detail_Transaksi;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Validator;

class TransaksiController extends Controller
{
    public function __construct()
{
    Config::$serverKey = config('midtrans.server_key');
    Config::$isProduction = config('midtrans.is_production');
    Config::$isSanitized = config('midtrans.is_sanitized');
    Config::$is3ds = config('midtrans.is_3ds');
}

    public function index()
    {
        // dd(config('midtrans.server_key'));
        $transaksi = Transaksi::with('detailTransaksi.menu')->get();
        return response()->json($transaksi);
    }

    public function show($id)
    {
        $transaksi = Transaksi::with('detailTransaksi.menu')->find($id);
        if (!$transaksi) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }
        return response()->json($transaksi);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_order' => 'required|string',
            'metode_pembayaran' => 'required|string',
            'total_transaksi' => 'required|numeric',
            'items' => 'required|array|min:1',
            'items.*.id_menu' => 'required|integer|exists:menu,id_menu',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.total_harga' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $lastNoUrut = (int) Transaksi::max('no_urut');

$transaksi = Transaksi::create([
    'no_urut' => $lastNoUrut + 1,
    'nama_order' => $request->nama_order,
    'metode_pembayaran' => $request->metode_pembayaran,
    'total_transaksi' => $request->total_transaksi,
]);


        // Simpan detail transaksi
        foreach ($request->items as $item) {
            Detail_Transaksi::create([
                'id_transaksi' => $transaksi->id_transaksi,
                'id_menu' => $item['id_menu'],
                'jumlah' => $item['jumlah'],
                'total_harga' => $item['total_harga'],
            ]);
        }

        // Jika metode pembayaran Midtrans, generate snap token
        if (strtolower($request->metode_pembayaran) === 'midtrans') {
            $payload = [
                'transaction_details' => [
                    'order_id' => 'TRX-' . $transaksi->id_transaksi . '-' . time(),
                    'gross_amount' => $request->total_transaksi,
                ],
                'customer_details' => [
                    'first_name' => $request->nama_order,
                ],
                'enabled_payments' => ['gopay', 'bank_transfer', 'shopeepay', 'indomaret', 'qris'], // Optional
            ];

            $snapToken = Snap::getSnapToken($payload);

            return response()->json([
                'message' => 'Transaksi berhasil dibuat',
                'transaksi' => $transaksi,
                'snap_token' => $snapToken,
            ]);
        }

        return response()->json([
            'message' => 'Transaksi berhasil dibuat',
            'transaksi' => $transaksi,
        ]);
    }
}
