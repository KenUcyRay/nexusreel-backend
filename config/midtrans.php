<?php

return [
    'server_key' => env('MIDTRANS_SERVER_KEY'),
    'client_key' => env('MIDTRANS_CLIENT_KEY'),
    'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    'is_sanitized' => env('MIDTRANS_IS_SANITIZED', true),
    'is_3ds' => env('MIDTRANS_IS_3DS', true),
    
    // Redirect URLs
    'finish_url' => env('APP_URL') . '/booking-success',
    'unfinish_url' => env('APP_URL') . '/payment',
    'error_url' => env('APP_URL') . '/payment',
];