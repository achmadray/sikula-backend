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


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSatuanTable extends Migration
{
    public function up(): void
    {
        Schema::create('satuan', function (Blueprint $table) {
            $table->id('id_satuan');
            $table->string('nama_satuan', 50);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('satuan');
    }
}


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
            $table->unsignedBigInteger('id_pengguna');
            $table->string('no_urut', 50)->unique();  // nomor urut transaksi
            $table->string('nama_order', 255);
            $table->string('metode_pembayaran', 100);
            $table->decimal('total_transaksi', 15, 2);
            $table->date('tanggal_transaksi');
            $table->enum('status_pembayaran', ['pending', 'lunas', 'gagal']);  // status pembayaran
            $table->timestamps();

            $table->foreign('id_pengguna')
                  ->references('id_pengguna')->on('pengguna')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('transaksi');
    }
}


<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKategoriTable extends Migration
{
    public function up()
    {
        Schema::create('kategori', function (Blueprint $table) {
            $table->id('id_kategori');
            $table->string('nama_kategori', 100);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('kategori');
    }
}

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMenuTable extends Migration
{
    public function up()
    {
        Schema::create('menu', function (Blueprint $table) {
            $table->id('id_menu');
            $table->unsignedBigInteger('id_kategori');
            $table->string('nama_menu', 100);
            $table->decimal('harga', 15, 2);
            $table->timestamps();

            $table->foreign('id_kategori')
                  ->references('id_kategori')->on('kategori')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('menu');
    }
}

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


