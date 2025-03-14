<?php
return [
    'api_prefix' => '/api/v1',
    'web_prefix' => '',

    'middleware' => [
        'api' => [
            \App\Middleware\AuthMiddleware::class,
            \App\Middleware\CorsMiddleware::class,
        ],
        'web' => [
            \App\Middleware\WebMiddleware::class,
        ]
    ],

    'patterns' => [
        'id' => '[0-9]+',
        'slug' => '[a-z0-9-]+',
    ]
];