<?php

return [

    'defaults' => [
        'guard'     => 'web',
        'passwords' => 'users',
    ],

    'guards' => [

        // Buyers
        'web' => [
            'driver'   => 'session',
            'provider' => 'users',
        ],

        // Sellers
        'seller' => [
            'driver'   => 'session',
            'provider' => 'sellers',
        ],

        // Admins
        'admin' => [
            'driver'   => 'session',
            'provider' => 'admins',
        ],
    ],

    'providers' => [

        'users' => [
            'driver' => 'eloquent',
            'model'  => App\Models\User::class,
        ],

        'sellers' => [
            'driver' => 'eloquent',
            'model'  => App\Models\Seller::class,
        ],

        'admins' => [
            'driver' => 'eloquent',
            'model'  => App\Models\Admin::class,
        ],
    ],

    'passwords' => [

        'users' => [
            'provider' => 'users',
            'table'    => 'user_password_reset_tokens',
            'expire'   => 60,
            'throttle' => 60,
        ],

        'sellers' => [
            'provider' => 'sellers',
            'table'    => 'seller_password_reset_tokens',
            'expire'   => 60,
            'throttle' => 60,
        ],

        'admins' => [
            'provider' => 'admins',
            'table'    => 'admin_password_reset_tokens',
            'expire'   => 30,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

];