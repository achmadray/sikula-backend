<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';
    protected $primaryKey = 'id_transaksi';


    protected $fillable = [
        'id_pengguna',
        'no_urut',
        'nama_order',
        'metode_pembayaran',
        'total_transaksi',
        'tanggal_transaksi',
        'status_pembayaran',
    ];

    public function pengguna()
    {
        return $this->belongsTo(Pengguna::class, 'id_pengguna');
    }

    public function detailTransaksi()
    {
        return $this->hasMany(Detail_Transaksi::class, 'id_transaksi');
    }
}
