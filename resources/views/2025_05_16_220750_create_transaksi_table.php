<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransaksiTable extends Migration
{
    public function up()
    {
        Schema::create('transaksi', function (Blueprint $table) {
            $table->id('id_transaksi');
            $table->string('no_urut', 50)->unique();  // nomor urut transaksi
            $table->string('nama_order', 255);
            $table->string('metode_pembayaran', 100);
            $table->decimal('total_transaksi', 15, 2);
            $table->date('tanggal_transaksi');
            $table->enum('status_pembayaran', ['pending', 'lunas', 'gagal']);  // status pembayaran
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaksi');
    }
}
