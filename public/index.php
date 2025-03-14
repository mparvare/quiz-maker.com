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
ini_set('display_errors', $_ENV['APP_DEBUG'] ?? 1);
ini_set('display_startup_errors', 1);

try {
    // لاگ‌های دقیق
    error_log("Full REQUEST_URI: " . $_SERVER['REQUEST_URI']);
    error_log("SCRIPT_NAME: " . $_SERVER['SCRIPT_NAME']);
    error_log("PHP_SELF: " . $_SERVER['PHP_SELF']);
    error_log("DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT']);
    
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
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}