<?php

namespace App\Http\Controllers\Api;

use Midtrans\Snap;
use Midtrans\Config;
use App\Models\Transaksi;
use App\Models\Detail_Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class TransaksiController extends Controller
{
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    private function generateNoUrut()
    {
        $date = date('Ymd');
        $latest = Transaksi::where('no_urut', 'like', "TRX-$date-%")
                           ->orderBy('id_transaksi', 'desc')
                           ->first();

        if (!$latest) {
            $number = 1;
        } else {
            $lastNumber = intval(substr($latest->no_urut, strrpos($latest->no_urut, '-') + 1));
            $number = $lastNumber + 1;
        }

        return 'TRX-' . $date . '-' . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    public function index()
    {
        $transaksis = Transaksi::with('detailTransaksi.menu')->get();

        return response()->json([
            'success' => true,
            'data' => $transaksis,
        ]);
    }

    // Detail 1 transaksi by id
    public function show($id)
    {
        $transaksi = Transaksi::with('detailTransaksi.menu')->find($id);

        if (!$transaksi) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $transaksi,
        ]);
    }

    // Simpan transaksi baru + detail dan generate snap token
    public function store(Request $request)
    {
        $request->validate([
            'id_pengguna' => 'required|integer|exists:pengguna,id_pengguna',
            'nama_order' => 'required|string',
            'metode_pembayaran' => 'required|string',
            'total_transaksi' => 'required|numeric',
            'items' => 'required|array|min:1',
            'items.*.id_menu' => 'required|integer|exists:menu,id_menu',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.total_harga' => 'required|numeric|min:0',
        ]);

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

            $transaksi->load('detailTransaksi.menu'); // eager load relasi detail transaksi

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dibuat.',
                'data' => [
                    'transaksi' => $transaksi,
                    'snap_token' => $snapToken,
                ],
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error tambah transaksi: ' . $e->getMessage(), ['trace' => $e->getTraceAsString()]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan transaksi.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Update transaksi (misal update status pembayaran)
    public function update(Request $request, $id)
    {
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan',
            ], 404);
        }

        $request->validate([
            'nama_order' => 'sometimes|string',
            'metode_pembayaran' => 'sometimes|string',
            'total_transaksi' => 'sometimes|numeric',
            'status_pembayaran' => 'sometimes|in:pending,lunas,gagal',
        ]);

        try {
            $transaksi->update($request->only([
                'nama_order',
                'metode_pembayaran',
                'total_transaksi',
                'status_pembayaran',
            ]));

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil diperbarui',
                'data' => $transaksi,
            ]);
        } catch (\Exception $e) {
            Log::error('Error update transaksi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal memperbarui transaksi',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    // Hapus transaksi + detail terkait
    public function destroy($id)
    {
        $transaksi = Transaksi::find($id);

        if (!$transaksi) {
            return response()->json([
                'success' => false,
                'message' => 'Transaksi tidak ditemukan',
            ], 404);
        }

        try {
            $transaksi->delete();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil dihapus',
            ]);
        } catch (\Exception $e) {
            Log::error('Error hapus transaksi: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus transaksi',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
