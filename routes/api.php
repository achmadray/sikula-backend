<?php

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AkunController;
use App\Http\Controllers\Api\MenuController;
use App\Http\Controllers\Api\TransaksiController;
use App\Http\Controllers\Api\BarangController;
use App\Http\Controllers\Api\SatuanController;
use App\Http\Controllers\Api\SuplierController;
use App\Http\Controllers\Api\KategoriController;
use App\Http\Controllers\Api\PenggunaController;
use App\Http\Controllers\api\DashboardController;
use App\Http\Controllers\Api\BarangMasukController;
use App\Http\Controllers\Api\BarangKeluarController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\DetailTransaksiController;
use App\Http\Controllers\AuthController;

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::middleware('auth:api')->post('/auth/me', [AuthController::class, 'me']);

Route::get('/pengguna/profil/{id_akun}', [PenggunaController::class, 'profil']);
Route::apiResource('akun', AkunController::class);
Route::apiResource('barang', BarangController::class);
Route::apiResource('satuan', SatuanController::class);
Route::apiResource('pengguna', PenggunaController::class);
Route::apiResource('barang_keluar', BarangKeluarController::class);
Route::apiResource('barang_masuk', BarangMasukController::class);
Route::apiResource('suplier', SuplierController::class);
Route::apiResource('kategori', KategoriController::class);
Route::apiResource('menu', MenuController::class);
Route::apiResource('detail_transaksi', DetailTransaksiController::class);
Route::get('/dashboard/stok-summary', [DashboardController::class, 'stokSummary']);
Route::post('/midtrans/notification', [TransaksiController::class, 'notification']);
Route::apiResource('transaksi', TransaksiController::class);
// Route::post('/transaksi', [TransaksiController::class, 'store']);
Route::apiResource('/notifications', NotificationController::class,);
