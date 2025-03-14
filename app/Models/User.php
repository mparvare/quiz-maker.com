<?php
namespace App\Models;

use Core\Database;
use Firebase\JWT\JWT;

class User {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function register($data) {
        $hashedPassword = password_hash($data['password'], PASSWORD_BCRYPT);
        
        $sql = "INSERT INTO users (email, password, full_name, role) VALUES (?, ?, ?, ?)";
        $this->db->query($sql, [
            $data['email'], 
            $hashedPassword, 
            $data['full_name'],
            $data['role'] ?? 'student'
        ]);

        return $this->db->getConnection()->lastInsertId();
    }

    public function generateToken($userId, $role) {
        $payload = [
            'user_id' => $userId,
            'role' => $role,
            'exp' => time() + 3600
        ];

        return JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');
    }
}