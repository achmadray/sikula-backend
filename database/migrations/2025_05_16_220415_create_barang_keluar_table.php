<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBarangKeluarTable extends Migration
{
    public function up()
    {
        Schema::create('barang_keluar', function (Blueprint $table) {
            $table->id('id_barang_keluar');
            $table->unsignedBigInteger('id_barang');
            $table->unsignedBigInteger('id_pengguna');
            $table->date('tanggal_keluar');
            $table->integer('jumlah');
            $table->integer('stok_keluar')->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('id_barang')
                  ->references('id_barang')->on('barang')
                  ->onDelete('cascade');


            $table->foreign('id_pengguna')
                  ->references('id_pengguna')->on('pengguna')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('barang_keluar');
    }
}
