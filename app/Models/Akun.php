<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Akun extends Model
{
    use HasFactory;

    protected $table = 'akun';
    protected $primaryKey = 'id_akun';
    public $timestamps = false;

    protected $fillable = [
        'username',
        'password',
        'level',
    ];

    public function pengguna()
    {
        return $this->hasOne(Pengguna::class, 'id_akun', 'id_akun');
    }
}
