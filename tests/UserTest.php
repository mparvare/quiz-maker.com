<?php
namespace Tests;

use PHPUnit\Framework\TestCase;
use App\Services\UserService;
use App\Models\User;

class UserTest extends TestCase
{
    private $userService;
    private $userModel;

    protected function setUp(): void
    {
        $this->userService = new UserService();
        $this->userModel = new User();
    }

    public function testGetUserProfile()
    {
        $userId = 1; // Test user ID

        $profile = $this->userService->getUserProfile($userId);

        $this->assertIsArray($profile);
        $this->assertArrayHasKey('user', $profile);
        $this->assertArrayHasKey('quizzes', $profile);
    }

    public function testUpdateProfile()
    {
        $userId = 1; // Test user ID
        $updateData = [
            'username' => 'updated_username',
            'email' => 'updated@example.com'
        ];

        $updatedUser = $this->userService->updateProfile($userId, $updateData);

        $this->assertIsArray($updatedUser);
        $this->assertEquals($updateData['username'], $updatedUser['username']);
        $this->assertEquals($updateData['email'], $updatedUser['email']);
    }
}