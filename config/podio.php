<?php

return [
    'base_url' => env('PODIO_BASE_URL', 'https://api.podio.com'),

    'auth' => [
        'client_id' => env('PODIO_CLIENT_ID', ''),
        'client_secret' => env('PODIO_CLIENT_SECRET', ''),

        // methods: password or app
        'method' => env('PODIO_AUTH_METHOD', 'password'),

        // method: password
        'username' => env('PODIO_USERNAME', ''),
        'password' => env('PODIO_PASSWORD', ''),

        // method: app
        'app_id' => env('PODIO_APP_ID', ''),
        'app_token' => env('PODIO_APP_TOKEN', ''),
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
