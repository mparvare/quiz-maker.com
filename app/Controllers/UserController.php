<?php
namespace App\Controllers;

use Core\Request;
use Core\Response;
use App\Services\UserService;
use App\Services\AuthService;
use Rakit\Validation\Validator;

class UserController
{
    private $userService;
    private $authService;
    private $validator;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->authService = new AuthService();
        $this->validator = new Validator();
    }

    public function profile(Request $request, Response $response)
    {
        $token = $request->getHeader('Authorization');

        try {
            $user = $this->authService->getCurrentUser($token);
            $profile = $this->userService->getUserProfile($user['id']);

            return $response->json([
                'status' => 'success',
                'profile' => $profile
            ]);
        } catch (\Exception $e) {
            return $response->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function updateProfile(Request $request, Response $response)
    {
        $token = $request->getHeader('Authorization');
        $data = $request->getBody();

        try {
            $user = $this->authService->getCurrentUser($token);

            $validation = $this->validator->make($data, [
                'username' => 'min:3|max:50',
                'email' => 'email',
                'password' => 'min:6'
            ]);

            $validation->validate();

            if ($validation->fails()) {
                return $response->json([
                    'status' => 'error',
                    'errors' => $validation->errors()->all()
                ], 400);
            }

            $updatedUser = $this->userService->updateProfile($user['id'], $data);

            return $response->json([
                'status' => 'success',
                'user' => $updatedUser
            ]);
        } catch (\Exception $e) {
            return $response->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}