<?php

return [
    'base_url' => env('PODIO_BASE_URL', 'https://api.podio.com'),

    'auth' => [
        'username' => env('PODIO_USERNAME', ''),
        'password' => env('PODIO_PASSWORD', ''),
        'client_id' => env('PODIO_CLIENT_ID', ''),
        'client_secret' => env('PODIO_CLIENT_SECRET', ''),
    ],

    'http' => [
        'timeout' => (int) env('PODIO_HTTP_TIMEOUT', 30),
        'connect_timeout' => (int) env('PODIO_HTTP_CONNECT_TIMEOUT', 10),
    ],

    'cache' => [
        'store' => env('PODIO_CACHE_STORE'),
        'key' => env('PODIO_CACHE_KEY', 'podio:access_token'),
    ],
];
