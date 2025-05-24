<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\Detail_Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransaksiController extends Controller
{
    // Constructor tetap ada kalau perlu, tapi tanpa konfigurasi Midtrans
    public function __construct()
    {
        // Kosongkan atau hapus konfigurasi Midtrans
    }

    private function generateNoUrut()
    {
        $date = date('Ymd');
        $latest = Transaksi::where('no_urut', 'like', "TRX-$date-%")
            ->orderBy('id_transaksi', 'desc')
            ->first();

        $number = $latest ? intval(substr($latest->no_urut, strrpos($latest->no_urut, '-') + 1)) + 1 : 1;

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

    public function store(Request $request)
    {
        $request->validate([
            'nama_order' => 'required|string',
            'metode_pembayaran' => 'required|string',
            'total_transaksi' => 'required|numeric',
            'items' => 'required|array|min:1',
            'items.*.id_menu' => 'required|exists:menu,id_menu',
            'items.*.jumlah' => 'required|integer|min:1',
            'items.*.total_harga' => 'required|numeric',
        ]);

        DB::beginTransaction();

        try {
            $noUrut = $this->generateNoUrut();

            $transaksi = Transaksi::create([
                'no_urut' => $noUrut,
                'nama_order' => $request->nama_order,
                'metode_pembayaran' => $request->metode_pembayaran,
                'total_harga' => $request->total_transaksi,
                'status_pembayaran' => 'pending', // tetap default pending
                'tanggal_transaksi' => date('Y-m-d'),
            ]);

            foreach ($request->items as $item) {
                Detail_Transaksi::create([
                    'id_transaksi' => $transaksi->id_transaksi,
                    'id_menu' => $item['id_menu'],
                    'jumlah' => $item['jumlah'],
                    'sub_total' => $item['total_harga'],
                ]);
            }

            // Bagian Midtrans Snap Token dimatikan / dihapus
            // Jadi tidak ada kode Midtrans sama sekali di sini

            DB::commit();

            return response()->json([
                'message' => 'Transaksi berhasil disimpan.',
                'data' => $transaksi,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Terjadi kesalahan saat menyimpan transaksi.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

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
            'nama_order' => 'sometimes|string|max:255',
            'metode_pembayaran' => 'sometimes|string|max:50',
            'total_transaksi' => 'sometimes|numeric|min:0',
            'status_pembayaran' => 'sometimes|in:pending,lunas,gagal',
        ]);

        try {
            $transaksi->update([
                'nama_order' => $request->nama_order ?? $transaksi->nama_order,
                'metode_pembayaran' => $request->metode_pembayaran ?? $transaksi->metode_pembayaran,
                'total_harga' => $request->total_transaksi ?? $transaksi->total_harga,
                'status_pembayaran' => $request->status_pembayaran ?? $transaksi->status_pembayaran,
            ]);

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
            $transaksi->detailTransaksi()->delete();
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

    // Method notifikasi Midtrans dimatikan sementara
    public function notification(Request $request)
    {
        return response()->json([
            'message' => 'Fitur notifikasi Midtrans dinonaktifkan sementara.',
        ], 503);
    }
}
