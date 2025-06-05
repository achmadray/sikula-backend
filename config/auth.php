<?php

return [

    'defaults' => [
        'guard' => 'api',
        'passwords' => 'akun',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'akun', // menggunakan provider 'akun'
        ],

        'api' => [
            'driver' => 'jwt',
            'provider' => 'akun',
            'hash' => false,
        ],
    ],

    'providers' => [
        'akun' => [
            'driver' => 'eloquent',
            'model' => App\Models\Akun::class,
        ],
    ],

    'passwords' => [
        'akun' => [
            'provider' => 'akun',
            'table' => 'password_reset_tokens',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

];
