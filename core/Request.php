<?php
namespace Core;

class Request {
    private $method;
    private $uri;
    private $queryParams;
    private $body;

    public function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'];
        
        // استخراج URI با حذف پیشوندهای اضافی
        $requestUri = $_SERVER['REQUEST_URI'];
        
        // تنظیمات برای حذف پیشوند
        $basePaths = [
            '/quiz-maker.com/public/',
            '/quiz-maker.com/public',
            '/public/',
            '/public',
            ''
        ];

        // حذف پیشوندهای اضافی
        $processedUri = $requestUri;
        foreach ($basePaths as $basePath) {
            if ($basePath === '' || strpos($requestUri, $basePath) === 0) {
                $processedUri = substr($requestUri, strlen($basePath));
                break;
            }
        }

        // تمیز کردن URI
        $uri = parse_url($processedUri, PHP_URL_PATH);
        $this->uri = '/' . trim($uri, '/') ?: '/';

        // لاگ برای دیباگ
        error_log("Original Request URI: " . $requestUri);
        error_log("Processed URI: " . $this->uri);
        error_log("Base URI: " . $processedUri);

        $this->queryParams = $_GET;
        $this->body = json_decode(file_get_contents('php://input'), true) ?? [];
    }

    public function getMethod() {
        return $this->method;
    }

    public function getUri() {
        return $this->uri;
    }

    public function getQueryParams($key = null, $default = null) {
        if ($key === null) {
            return $this->queryParams;
        }
        return $this->queryParams[$key] ?? $default;
    }

    public function getBody($key = null, $default = null) {
        if ($key === null) {
            return $this->body;
        }
        return $this->body[$key] ?? $default;
    }
}