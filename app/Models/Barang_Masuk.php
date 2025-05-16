<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang_Masuk extends Model
{
    use HasFactory;

    protected $table = 'barang_masuk';
    protected $primaryKey = 'id_barang_masuk';

    protected $fillable = [
        'id_suplier',
        'id_barang',
        'harga',
        'jumlah',
        'total_harga',
        'stok_masuk',
        'tanggal_masuk',
    ];

    public function suplier()
    {
        return $this->belongsTo(Suplier::class, 'id_suplier');
    }

    public function barang()
    {
        return $this->belongsTo(Barang::class, 'id_barang');
    }
}
