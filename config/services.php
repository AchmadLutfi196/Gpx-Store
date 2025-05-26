<?php

return [
        'google' => [
            'client_id' => env('GOOGLE_CLIENT_ID'),
            'client_secret' => env('GOOGLE_CLIENT_SECRET'),
            'redirect' => env('GOOGLE_REDIRECT_URI'),
            ],
        'github' => [
            'client_id' => env('GITHUB_CLIENT_ID'),
            'client_secret' => env('GITHUB_CLIENT_SECRET'),
            'redirect' => env('GITHUB_REDIRECT'),
            ],
        'rajaongkir' => [
        'key' => env('RAJAONGKIR_API_KEY', env('KOMERCE_API_KEY', '')),
        'url' => env('RAJAONGKIR_URL', 'https://api.rajaongkir.com/starter'),
        'sandbox_url' => env('RAJAONGKIR_SANDBOX_URL', 'https://api.komerce.id/api/ongkir/v2'),
        'sandbox' => env('RAJAONGKIR_SANDBOX', true),
        'production' => env('RAJAONGKIR_PRODUCTION', false),
        'use_static_data' => env('RAJAONGKIR_USE_STATIC_DATA', true), // Enable static data by default
    ],
];