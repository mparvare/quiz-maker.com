<?php
namespace App\Controllers;

use Core\Request;
use Core\Response;

class HomeController {
    public function index(Request $request, Response $response) {
        // لاگ برای دیباگ
        error_log("HomeController::index called");
        
        $response->json([
            'message' => 'Welcome to Quiz Maker',
            'uri' => $request->getUri(),
            'method' => $request->getMethod(),
            'full_request_uri' => $_SERVER['REQUEST_URI']
        ]);
    }

    public function debug(Request $request, Response $response) {
        $response->json([
            'server_vars' => [
                'REQUEST_URI' => $_SERVER['REQUEST_URI'],
                'SCRIPT_NAME' => $_SERVER['SCRIPT_NAME'],
                'PHP_SELF' => $_SERVER['PHP_SELF'],
                'DOCUMENT_ROOT' => $_SERVER['DOCUMENT_ROOT']
            ],
            'request_details' => [
                'method' => $request->getMethod(),
                'uri' => $request->getUri()
            ]
        ]);
    }
}