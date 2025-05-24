<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBarangMasukTable extends Migration
{
    public function up()
    {
        Schema::create('barang_masuk', function (Blueprint $table) {
            $table->id('id_barang_masuk');
            $table->unsignedBigInteger('id_suplier');
            $table->unsignedBigInteger('id_barang');
            $table->unsignedBigInteger('id_pengguna');
            $table->decimal('harga', 15, 2);
            $table->integer('jumlah');
            $table->decimal('total_harga', 15, 2);
            $table->integer('stok_masuk');
            $table->date('tanggal_masuk');
            $table->timestamps();

            $table->foreign('id_suplier')
                  ->references('id_suplier')->on('suplier')
                  ->onDelete('cascade');

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
        Schema::dropIfExists('barang_masuk');
    }
}
