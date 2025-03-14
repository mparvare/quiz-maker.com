<?php
return function($router) {
    // مسیرهای پایه
    $router->addRoute('GET', '/', [App\Controllers\HomeController::class, 'index']);
    $router->addRoute('GET', '/index', [App\Controllers\HomeController::class, 'index']);
    $router->addRoute('GET', '/home', [App\Controllers\HomeController::class, 'index']);
    
    // مسیر دیباگ
    $router->addRoute('GET', '/debug', [App\Controllers\HomeController::class, 'debug']);
    
    // مسیرهای احراز هویت
    $router->addRoute('GET', '/login', [App\Controllers\AuthController::class, 'loginForm']);
    $router->addRoute('POST', '/login', [App\Controllers\AuthController::class, 'login']);
    
    // مسیرهای API
    $apiPrefix = '/api/v1';
    
    // مسیرهای کاربران
    $router->addRoute('GET', $apiPrefix.'/users', [App\Controllers\Api\UserController::class, 'index']);
    $router->addRoute('GET', $apiPrefix.'/users/{id}', [App\Controllers\Api\UserController::class, 'show']);
};