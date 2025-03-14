<?php
namespace Src\Controllers;

use Core\Request;
use Core\Response;
use Src\Models\User;
use Src\Repositories\UserRepository;

class UserController {
    private $userRepository;

    public function __construct() {
        $this->userRepository = new UserRepository();
    }

    public function getUsers(Request $request, Response $response) {
        $page = (int) $request->getQueryParams('page', 1);
        $perPage = (int) $request->getQueryParams('per_page', 10);
        
        $users = $this->userRepository->findAll($page, $perPage);
        $total = $this->userRepository->countTotal();
        
        $data = [
            'users' => array_map(function($user) {
                return $user->toArray();
            }, $users),
            'pagination' => [
                'total' => $total,
                'per_page' => $perPage,
                'current_page' => $page,
                'last_page' => ceil($total / $perPage)
            ]
        ];
        
        $response->success('کاربران با موفقیت دریافت شدند', $data);
    }

    public function getUserById(Request $request, Response $response, $id) {
        $user = $this->userRepository->findById($id);
        
        if (!$user) {
            $response->error('کاربر یافت نشد', 404);
        }
        
        $response->success('کاربر با موفقیت دریافت شد', $user->toArray());
    }

    public function createUser(Request $request, Response $response) {
        $email = $request->getBody('email');
        $username = $request->getBody('username');
        $password = $request->getBody('password');
        $fullName = $request->getBody('full_name');
        $role = $request->getBody('role', 'student');
        
        // اعتبارسنجی
        if (empty($email) || empty($username) || empty($password) || empty($fullName)) {
            $response->error('لطفا تمامی فیلدها را پر کنید');
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response->error('ایمیل نامعتبر است');
        }
        
        // بررسی تکراری بودن
        if ($this->userRepository->findByEmail($email)) {
            $response->error('این ایمیل قبلا ثبت شده است');
        }
        
        if ($this->userRepository->findByUsername($username)) {
            $response->error('این نام کاربری قبلا ثبت شده است');
        }
        
        // ایجاد کاربر
        $user = new User();
        $user->setEmail($email)
        ->setUsername($username)
        ->setPassword($password)
        ->setFullName($fullName)
        ->setRole($role)
        ->setActive(true);

        $createdUser = $this->userRepository->create($user);
        
        $response->success('کاربر با موفقیت ایجاد شد', $createdUser->toArray(), 201);
    }

    public function updateUser(Request $request, Response $response, $id) {
        $user = $this->userRepository->findById($id);
        
        if (!$user) {
            $response->error('کاربر یافت نشد', 404);
        }

        $email = $request->getBody('email');
        $username = $request->getBody('username');
        $fullName = $request->getBody('full_name');
        $role = $request->getBody('role');
        $active = $request->getBody('active');

        // اعتبارسنجی
        if (empty($email) || empty($username) || empty($fullName)) {
            $response->error('لطفا تمامی فیلدهای اجباری را پر کنید');
        }

        // بررسی تکراری بودن ایمیل
        $existingEmail = $this->userRepository->findByEmail($email);
        if ($existingEmail && $existingEmail->getId() !== $id) {
            $response->error('این ایمیل توسط کاربر دیگری استفاده شده است');
        }

        // بررسی تکراری بودن نام کاربری
        $existingUsername = $this->userRepository->findByUsername($username);
        if ($existingUsername && $existingUsername->getId() !== $id) {
            $response->error('این نام کاربری توسط کاربر دیگری استفاده شده است');
        }

        // بروزرسانی اطلاعات
        $user->setEmail($email)
             ->setUsername($username)
             ->setFullName($fullName)
             ->setRole($role ?? $user->getRole())
             ->setActive($active ?? $user->isActive());

        $updatedUser = $this->userRepository->update($user);
        
        $response->success('اطلاعات کاربر با موفقیت بروزرسانی شد', $updatedUser->toArray());
    }

    public function deleteUser(Request $request, Response $response, $id) {
        $user = $this->userRepository->findById($id);
        
        if (!$user) {
            $response->error('کاربر یافت نشد', 404);
        }

        // حذف کاربر
        $this->userRepository->delete($id);
        
        $response->success('کاربر با موفقیت حذف شد');
    }

    public function login(Request $request, Response $response) {
        $login = $request->getBody('login');
        $password = $request->getBody('password');

        if (empty($login) || empty($password)) {
            $response->error('نام کاربری/ایمیل و رمز عبور را وارد کنید');
        }

        // تلاش برای یافتن کاربر با ایمیل یا نام کاربری
        $user = $this->userRepository->findByEmail($login) ?? 
                $this->userRepository->findByUsername($login);

        if (!$user) {
            $response->error('کاربری با این مشخصات یافت نشد', 401);
        }

        // بررسی رمز عبور
        if (!password_verify($password, $user->getPassword())) {
            $response->error('رمز عبور نادرست است', 401);
        }

        // تولید توکن
        $token = $this->generateToken($user);

        $response->success('ورود موفقیت‌آمیز', [
            'user' => $user->toArray(),
            'token' => $token
        ]);
    }

    private function generateToken($user) {
        $secretKey = 'YOUR_SECRET_KEY'; // کلید امنیتی را از محیط تنظیمات دریافت کنید
        $issuedAt = time();
        $expirationTime = $issuedAt + 3600; // توکن تا 1 ساعت اعتبار دارد

        $payload = [
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'userId' => $user->getId(),
            'email' => $user->getEmail(),
            'role' => $user->getRole()
        ];

        return $this->encodeToken($payload, $secretKey);
    }

    private function encodeToken($payload, $secretKey) {
        $header = json_encode([
            'typ' => 'JWT',
            'alg' => 'HS256'
        ]);

        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode(json_encode($payload)));

        $signature = hash_hmac('sha256', "$base64UrlHeader.$base64UrlPayload", $secretKey, true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));

        return "$base64UrlHeader.$base64UrlPayload.$base64UrlSignature";
    }
}