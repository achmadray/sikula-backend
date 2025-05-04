<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBarangTable extends Migration
{
    public function up(): void
    {
        Schema::create('barang', function (Blueprint $table) {
            $table->id('id_barang');
            $table->string('nama_barang', 100);
            $table->unsignedBigInteger('id_satuan');
            $table->string('kode_barang', 50)->unique();
            $table->unsignedBigInteger('id_pengguna');
            $table->integer('stok')->default(0);
            $table->timestamps();

            $table->foreign('id_satuan')->references('id_satuan')->on('satuan')->onDelete('cascade');
            $table->foreign('id_pengguna')->references('id_pengguna')->on('pengguna')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barang');
    }
}
