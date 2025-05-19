<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Midtrans\Snap;
use Midtrans\Config;

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
        return response()->json(Transaksi::all());
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_pengguna' => 'required|exists:pengguna,id_pengguna',
            'nama_order' => 'required|string',
            'metode_pembayaran' => 'required|string',
            'total_transaksi' => 'required|numeric',
        ]);

        $noUrut = $this->generateNoUrut();

        $transaksi = Transaksi::create([
            'id_pengguna' => $request->id_pengguna,
            'no_urut' => $noUrut,
            'nama_order' => $request->nama_order,
            'metode_pembayaran' => $request->metode_pembayaran,
            'total_transaksi' => $request->total_transaksi,
            'tanggal_transaksi' => now(),
            'status_pembayaran' => 'pending',
        ]);

        $snapToken = Snap::getSnapToken([
            'transaction_details' => [
                'order_id' => $noUrut,
                'gross_amount' => $request->total_transaksi,
            ],
            'customer_details' => [
                'first_name' => $request->nama_order,
            ],
            'enabled_payments' => ['gopay', 'bank_transfer', 'shopeepay', 'indomaret', 'qris'],
        ]);

        return response()->json([
            'message' => 'Transaksi berhasil dibuat.',
            'transaksi' => $transaksi,
            'snap_token' => $snapToken,
        ]);
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
