<?php
return [
    'name' => env('APP_NAME', 'Quiz Maker'),
    'env' => env('APP_ENV', 'production'),
    'debug' => env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    
    'timezone' => 'UTC',
    'locale' => 'en',
    
    'providers' => [
        // Liste of service providers
        \App\Providers\AuthServiceProvider::class,
        \App\Providers\RouteServiceProvider::class,
    ],

    'aliases' => [
        'App' => \Core\App::class,
        'Request' => \Core\Request::class,
        'Response' => \Core\Response::class,
        'Database' => \Core\Database::class,
    ]
];