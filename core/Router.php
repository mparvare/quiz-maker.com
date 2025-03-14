<?php
namespace Core;

class Router {
    private $routes = [];

    public function addRoute($method, $path, $handler) {
        // اطمینان از شروع مسیر با "/"
        $path = '/' . trim($path, '/');

        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function dispatch(Request $request, Response $response) {
        $uri = $request->getUri();
        $method = $request->getMethod();

        // لاگ برای دیباگ
        error_log("Dispatching: Method = $method, URI = $uri");

        // نمایش تمام مسیرهای ثبت شده
        $this->logRegisteredRoutes();

        foreach ($this->routes as $route) {
            // مچ کردن متد
            if ($route['method'] !== $method) {
                continue;
            }

            // مچ کردن مسیرهای ساده
            if ($route['path'] === $uri) {
                return $this->invokeHandler($route['handler'], $request, $response);
            }

            // مچ کردن مسیرهای با پارامتر
            if (strpos($route['path'], '{') !== false) {
                $routePattern = $this->convertRouteToRegex($route['path']);
                
                if (preg_match($routePattern, $uri, $matches)) {
                    return $this->invokeHandler($route['handler'], $request, $response, $matches);
                }
            }
        }

        // مسیر یافت نشد
        $response->error('Route Not Found', 404);
    }

    private function convertRouteToRegex($route) {
        $routePattern = preg_replace('/\{([^}]+)\}/', '(?P<\1>[^/]+)', $route);
        return '#^' . str_replace('/', '\/', $routePattern) . '$#';
    }

    private function invokeHandler($handler, Request $request, Response $response, $matches = []) {
        // ایجاد نمونه از کنترلر
        if (is_array($handler) && count($handler) === 2) {
            $controllerClass = $handler[0];
            $methodName = $handler[1];
            
            // ایجاد نمونه از کلاس کنترلر
            $controllerInstance = new $controllerClass();
            
            // استخراج پارامترها
            $params = [];
            if (!empty($matches)) {
                // حذف مچ کامل و مسیرهای عددی
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
            }
            
            // فراخوانی متد
            return call_user_func_array(
                [$controllerInstance, $methodName], 
                array_merge([$request, $response], array_values($params))
            );
        }
        
        // اگر هندلر یک تابع است
        return call_user_func($handler, $request, $response);
    }

    private function logRegisteredRoutes() {
        error_log("Registered Routes:");
        foreach ($this->routes as $route) {
            error_log("Method: {$route['method']}, Path: {$route['path']}");
        }
    }
}