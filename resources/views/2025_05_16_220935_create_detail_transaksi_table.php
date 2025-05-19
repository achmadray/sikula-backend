<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDetailTransaksiTable extends Migration
{
    public function up()
    {
        Schema::create('detail_transaksi', function (Blueprint $table) {
            $table->id('id_detail_transaksi');
            $table->unsignedBigInteger('id_menu');
            $table->unsignedBigInteger('id_transaksi');
            $table->integer('jumlah');
            $table->decimal('total_harga', 15, 2);
            $table->timestamps();

            $table->foreign('id_menu')
                  ->references('id_menu')->on('menu')
                  ->onDelete('cascade');

            $table->foreign('id_transaksi')
                  ->references('id_transaksi')->on('transaksi')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('detail_transaksi');
    }
}
