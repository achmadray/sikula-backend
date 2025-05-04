<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Suplier extends Model
{
    use HasFactory;

    protected $table = 'suplier';

    protected $fillable = [
        'nama_suplier',
        'no_telpon',
        'alamat',
    ];

    public function barangMasuk()
    {
        return $this->hasMany(BarangMasuk::class, 'id_suplier');
    }
}
