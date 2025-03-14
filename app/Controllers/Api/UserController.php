<?php
namespace App\Controllers\Api;

use Core\Request;
use Core\Response;

class UserController {
    public function index(Request $request, Response $response) {
        // نمایش لیست کاربران
        $response->json([
            'message' => 'User List',
            'users' => []
        ]);
    }

    public function show(Request $request, Response $response, $id) {
        // نمایش جزئیات یک کاربر
        $response->json([
            'message' => 'User Details',
            'user_id' => $id
        ]);
    }
}