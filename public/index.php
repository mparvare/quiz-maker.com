<?php
require_once __DIR__ . '/../vendor/autoload.php';

use Core\Request;
use Core\Router;
use Core\Response;
use Dotenv\Dotenv;

// بارگذاری متغیرهای محیطی
$dotenv = Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();

// تنظیمات اولیه
error_reporting(E_ALL);
ini_set('display_errors', $_ENV['APP_DEBUG'] ?? 0);

try {
    $request = new Request();
    $response = new Response();
    $router = new Router();

    // بارگذاری مسیرها
    $routeDefinition = require_once '../config/routes.php';
    $routeDefinition($router);

    // پردازش درخواست
    $router->dispatch($request, $response);
} catch (\Exception $e) {
    // مدیریت خطا
    http_response_code(500);
    echo json_encode([
        'error' => 'Internal Server Error',
        'message' => $e->getMessage()
    ]);
}