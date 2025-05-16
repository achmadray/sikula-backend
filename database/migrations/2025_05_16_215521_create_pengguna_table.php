<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePenggunaTable extends Migration
{
    public function up(): void
    {
        Schema::create('pengguna', function (Blueprint $table) {
            $table->id('id_pengguna');
            $table->string('nama_pengguna', 100);
            $table->string('email')->unique();
            $table->unsignedBigInteger('id_akun');
            $table->string('no_telpon', 20)->nullable();
            $table->timestamps();

            $table->foreign('id_akun')->references('id_akun')->on('akun')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pengguna');
    }
}
