<?php
namespace App\Middleware;

use Core\Request;
use Core\Response;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthMiddleware {
    public static function handle(Request $request, Response $response, $requiredRoles = []) {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? null;

        if (!$authHeader) {
            $response->error('توکن احراز هویت یافت نشد', 401);
        }

        try {
            $token = str_replace('Bearer ', '', $authHeader);
            $decoded = JWT::decode($token, new Key(env('JWT_SECRET'), 'HS256'));

            // بررسی نقش کاربر
            if (!empty($requiredRoles) && !in_array($decoded->role, $requiredRoles)) {
                $response->error('دسترسی غیرمجاز', 403);
            }

            return $decoded;
        } catch (\Exception $e) {
            $response->error('توکن نامعتبر است', 401);
        }
    }
}
?>