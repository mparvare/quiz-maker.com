<?php
namespace App\Services;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class TokenService {
    public static function generateToken($user) {
        $payload = [
            'sub' => $user->id,
            'email' => $user->email,
            'iat' => time(),
            'exp' => time() + getenv('JWT_EXPIRATION')
        ];

        return JWT::encode($payload, getenv('JWT_SECRET'), 'HS256');
    }

    public static function generateRefreshToken($user) {
        $payload = [
            'sub' => $user->id,
            'type' => 'refresh',
            'iat' => time(),
            'exp' => time() + getenv('REFRESH_TOKEN_EXPIRATION')
        ];

        return JWT::encode($payload, getenv('JWT_SECRET'), 'HS256');
    }

    public static function verifyToken($token) {
        try {
            return JWT::decode(
                $token, 
                new Key(getenv('JWT_SECRET'), 'HS256')
            );
        } catch (\Exception $e) {
            return false;
        }
    }
}