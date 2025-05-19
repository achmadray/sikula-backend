<?php
namespace App\Http\Controllers\Api;

use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use App\Models\Detail_Transaksi;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class TransaksiController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.sanitized');
        Config::$is3ds = config('midtrans.3ds');
    }

    private function generateNoUrut()
    {
        $latest = Transaksi::orderBy('id_transaksi', 'desc')->first();
        $number = $latest ? intval(substr($latest->no_urut, -5)) + 1 : 1;
        return 'TRX' . str_pad($number, 5, '0', STR_PAD_LEFT);
    }

    public function index()
    {
        $transaksi = Transaksi::with(['pengguna'])->get();
        return response()->json($transaksi);
    }

    public function store(Request $request)
{
    DB::beginTransaction();

    try {
        $transaksi = Transaksi::create([
            'id_pengguna' => $request->id_pengguna,
            'no_urut' => $this->generateNoUrut(),
            'nama_order' => $request->nama_order,
            'metode_pembayaran' => $request->metode_pembayaran,
            'total_transaksi' => $request->total_transaksi,
            'tanggal_transaksi' => now(),
            'status_pembayaran' => 'pending',
        ]);

        foreach ($request->items as $item) {
            Detail_Transaksi::create([
                'id_transaksi' => $transaksi->id_transaksi,
                'id_menu' => $item['id_menu'],
                'jumlah' => $item['jumlah'],
                'total_harga' => $item['total_harga'],
            ]);
        }

        $snapToken = Snap::getSnapToken([
            'transaction_details' => [
                'order_id' => $transaksi->no_urut,
                'gross_amount' => $transaksi->total_transaksi,
            ],
            'customer_details' => [
                'first_name' => $transaksi->nama_order,
            ],
            'enabled_payments' => ['gopay', 'bank_transfer', 'shopeepay', 'indomaret', 'qris'],
        ]);

        DB::commit();

        return response()->json([
            'message' => 'Transaksi berhasil dibuat.',
            'transaksi' => $transaksi,
            'snap_token' => $snapToken,
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['message' => 'Terjadi kesalahan saat menyimpan transaksi.'], 500);
    }
}

    public function show($id)
    {
        $transaksi = Transaksi::find($id);
        return $transaksi
            ? response()->json($transaksi)
            : response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
    }

    public function destroy($id)
    {
        $trx = Transaksi::find($id);
        if (!$trx) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }
        $trx->delete();
        return response()->json(['message' => 'Transaksi berhasil dihapus']);
    }

    public function paymentToken($id)
    {
        $trx = Transaksi::find($id);
        if (!$trx) {
            return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);
        }

        $snapToken = Snap::getSnapToken([
            'transaction_details' => [
                'order_id' => $trx->no_urut,
                'gross_amount' => $trx->total_transaksi,
            ],
            'customer_details' => [
                'first_name' => $trx->nama_order,
            ],
        ]);

        return response()->json(['snap_token' => $snapToken]);
    }

    public function callback(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        $signatureKey = $request->signature_key;
        $orderId = $request->order_id;
        $statusCode = $request->status_code;
        $grossAmount = $request->gross_amount;

        $trx = Transaksi::where('no_urut', $orderId)->first();
        if (!$trx) return response()->json(['message' => 'Transaksi tidak ditemukan'], 404);

        $hashed = hash('sha512', $orderId . $statusCode . $grossAmount . $serverKey);
        if ($hashed !== $signatureKey) return response()->json(['message' => 'Signature tidak valid'], 403);

        if (in_array($request->transaction_status, ['capture', 'settlement'])) {
            $trx->update(['status_pembayaran' => 'lunas']);
        }

        return response()->json(['message' => 'Status pembayaran diperbarui']);
    }
}
