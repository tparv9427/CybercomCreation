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
    private $cartRepo;
    private $wishlistRepo;

    public function __construct()
    {
        $this->userRepo = new UserRepository();
        $this->cartRepo = new \EasyCart\Repositories\CartRepository();
        $this->wishlistRepo = new \EasyCart\Repositories\WishlistRepository();
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
        $this->mergeGuestData($user['id']);

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

            // Merge guest data
            $this->mergeGuestData($userId);
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
        unset($_SESSION['cart_id']);
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

    /**
     * Merge guest data into user account
     */
    private function mergeGuestData($userId)
    {
        // Transfer cart ownership from guest to user
        $this->cartRepo->transferGuestCartToUser($userId);

        // Merge Wishlist
        if (isset($_SESSION['guest_wishlist']) && !empty($_SESSION['guest_wishlist'])) {
            foreach ($_SESSION['guest_wishlist'] as $pid => $val) {
                $this->wishlistRepo->add($userId, $pid);
            }
            unset($_SESSION['guest_wishlist']);
        }
    }
}
