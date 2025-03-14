<?php
namespace App\Services;

use Respect\Validation\Validator as v;

class AuthService {
    public function validateRegistration($data) {
        $errors = [];

        // اعتبارسنجی ایمیل
        if (!v::email()->validate($data['email'])) {
            $errors[] = 'Invalid email format';
        }

        // اعتبارسنجی رمز عبور
        if (!v::stringType()->length(6, 20)->validate($data['password'])) {
            $errors[] = 'Password must be 6-20 characters';
        }

        return [
            'valid' => count($errors) === 0,
            'errors' => $errors
        ];
    }
}