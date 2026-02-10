<?php

namespace EasyCart\Services;

use EasyCart\Resource\Resource_User;
use EasyCart\Core\Validation;

/**
 * EmailCartService — Email-first add-to-cart business logic
 * 
 * MVC Rule: Before adding to cart, check email.
 * If email exists → ask for password before proceeding.
 * If new email → allow guest checkout or prompt signup.
 */
class EmailCartService
{
    private $userResource;

    public function __construct()
    {
        $this->userResource = new Resource_User();
    }

    /**
     * Check if an email exists in the system
     * 
     * @param string $email
     * @return array ['exists' => bool, 'user_id' => int|null, 'is_active' => bool]
     */
    public function checkEmail(string $email): array
    {
        // Validate email format
        $validation = new Validation(['email' => $email]);
        $validation->required('email', 'Email is required')
            ->email('email', 'Invalid email format');

        if ($validation->fails()) {
            return [
                'valid' => false,
                'exists' => false,
                'user_id' => null,
                'is_active' => false,
                'errors' => $validation->errors()
            ];
        }

        // Look up email in database
        $user = $this->userResource->findByEmail($email);

        if ($user) {
            return [
                'valid' => true,
                'exists' => true,
                'user_id' => $user['id'] ?? $user['entity_id'] ?? null,
                'is_active' => (bool) ($user['is_active'] ?? true),
                'requires_password' => true
            ];
        }

        return [
            'valid' => true,
            'exists' => false,
            'user_id' => null,
            'is_active' => false,
            'requires_password' => false
        ];
    }

    /**
     * Verify password for existing user before allowing cart actions
     * 
     * @param string $email
     * @param string $password
     * @return array ['authenticated' => bool, 'user_id' => int|null, 'error' => string|null]
     */
    public function verifyAndAuthenticate(string $email, string $password): array
    {
        $user = $this->userResource->findByEmail($email);

        if (!$user) {
            return [
                'authenticated' => false,
                'user_id' => null,
                'error' => 'No account found with this email'
            ];
        }

        if (!($user['is_active'] ?? true)) {
            return [
                'authenticated' => false,
                'user_id' => null,
                'error' => 'This account has been deactivated'
            ];
        }

        $passwordHash = $user['password'] ?? $user['password_hash'] ?? '';
        if (!password_verify($password, $passwordHash)) {
            return [
                'authenticated' => false,
                'user_id' => null,
                'error' => 'Incorrect password'
            ];
        }

        return [
            'authenticated' => true,
            'user_id' => $user['id'] ?? $user['entity_id'] ?? null,
            'error' => null
        ];
    }
}
