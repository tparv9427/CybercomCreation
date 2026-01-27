<?php

namespace EasyCart\Services;

use EasyCart\Repositories\UserRepository;

/**
 * AuthService
 * 
 * Migrated from: login.php, signup.php, logout.php, config.php (isLoggedIn)
 */
class AuthService
{
    private $userRepo;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
    }

    /**
     * Check if user is logged in
     * 
     * @return bool
     */
    public static function check()
    {
        if (!isset($_SESSION)) {
            session_start();
        }
        return isset($_SESSION['user_id']) && $_SESSION['user_id'] !== null;
    }

    /**
     * Login user
     * 
     * @param string $email
     * @param string $password
     * @return array|false User array on success, false on failure
     */
    public function login($email, $password)
    {
        $user = $this->userRepo->findByEmail($email);
        
        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user['password'])) {
            return false;
        }

        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];

        // Merge guest data
        SessionService::mergeGuestData($user['id']);

        return $user;
    }

    /**
     * Register new user
     * 
     * @param string $email
     * @param string $password
     * @param string $name
     * @return int|false User ID on success, false on failure
     */
    public function register($email, $password, $name)
    {
        $userId = $this->userRepo->create($email, $password, $name);
        
        if ($userId) {
            // Auto-login
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;

            // Merge guest data
            SessionService::mergeGuestData($userId);
        }

        return $userId;
    }

    /**
     * Logout user
     */
    public function logout()
    {
        // Clear session
        $_SESSION['user_id'] = null;
        $_SESSION['user_name'] = null;
        $_SESSION['user_email'] = null;
        unset($_SESSION['cart']);
        unset($_SESSION['wishlist']);
    }

    /**
     * Get current user
     * 
     * @return array|null
     */
    public function getCurrentUser()
    {
        if (!self::check()) {
            return null;
        }

        return $this->userRepo->find($_SESSION['user_id']);
    }
}
