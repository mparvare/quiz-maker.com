<?php
namespace Core;

class Router {
    private $routes = [];

    public function addRoute($method, $path, $handler) {
        $this->routes[] = [
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler
        ];
    }

    public function dispatch(Request $request, Response $response) {
        $uri = $request->getUri();
        $method = $request->getMethod();

        foreach ($this->routes as $route) {
            // Exact match or pattern matching
            if ($route['method'] === $method && 
                (($route['path'] === $uri) || 
                 (strpos($route['path'], '{') !== false && $this->matchRoute($route['path'], $uri)))) {
                
                $handler = $route['handler'];
                
                // Dependency Injection
                return call_user_func($handler, $request, $response);
            }
        }

        // Route not found
        $response->error('Route Not Found', 404);
    }

    private function matchRoute($routePath, $uri) {
        // Basic route parameter matching
        $routeRegex = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '([^/]+)', $routePath);
        $routeRegex = str_replace('/', '\/', $routeRegex);
        
        if (preg_match("/^{$routeRegex}$/", $uri)) {
            return true;
        }
        
        return false;
    }
}
?>