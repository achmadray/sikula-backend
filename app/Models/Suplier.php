<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suplier extends Model
{
    use HasFactory;

    protected $table = 'suplier';

    protected $primaryKey = 'id_suplier';
    protected $fillable = [
        'nama_suplier',
        'no_telpon',
        'alamat',
    ];

    public function Barang_Masuk()
    {
        return $this->hasMany(Barang_Masuk::class, 'id_suplier');
    }
}
