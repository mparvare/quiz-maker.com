<?php
namespace App\Middleware;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Services\TokenService;

class AuthMiddleware {
    public function handle($request) {
        $token = $this->extractToken($request);

        try {
            $decoded = JWT::decode(
                $token, 
                new Key(getenv('JWT_SECRET'), 'HS256')
            );

            // اعتبارسنجی توکن
            if ($this->isTokenExpired($decoded)) {
                throw new \Exception('توکن منقضی شده است');
            }

            // افزودن اطلاعات کاربر به درخواست
            $request->user = $decoded;
            return true;
        } catch (\Exception $e) {
            // رد درخواست
            http_response_code(401);
            echo json_encode([
                'status' => 'error',
                'message' => 'احراز هویت ناموفق'
            ]);
            exit;
        }
    }

    private function extractToken($request) {
        $headers = getallheaders();
        $authHeader = $headers['Authorization'] ?? '';
        
        preg_match('/Bearer\s(\S+)/', $authHeader, $matches);
        return $matches[1] ?? null;
    }

    private function isTokenExpired($decoded) {
        return $decoded->exp < time();
    }
}