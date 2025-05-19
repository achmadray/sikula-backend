<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSuplierTable extends Migration
{
    public function up()
    {
        Schema::create('suplier', function (Blueprint $table) {
            $table->id('id_suplier');
            $table->string('nama_suplier', 100);
            $table->string('no_telpon', 20)->nullable();
            $table->text('alamat')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('suplier');
    }
}
