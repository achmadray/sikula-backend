<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;  // perhatikan extend ini
use Tymon\JWTAuth\Contracts\JWTSubject;

class Akun extends Authenticatable implements JWTSubject
{
    use HasFactory;

    protected $table = 'akun';
    protected $primaryKey = 'id_akun';
    public $timestamps = false;

    protected $fillable = [
        'username',
        'password',
        'level'
    ];

    protected $hidden = [
        'password',
    ];

    // JWTSubject interface methods
    public function getJWTIdentifier()
    {
        return $this->getKey();  // primary key
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    // Relasi
    public function pengguna()
    {
        return $this->hasOne(Pengguna::class, 'id_akun', 'id_akun');
    }
}

