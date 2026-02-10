<?php

namespace EasyCart\Services;

use EasyCart\Resource\Resource_User;
use EasyCart\Resource\Resource_Cart;
use EasyCart\Resource\Resource_Wishlist;
use EasyCart\Database\QueryBuilder;

/**
 * AuthService
 * 
 * Migrated from: login.php, signup.php, logout.php, config.php (isLoggedIn)
 * Uses Resources instead of Legacy Repositories.
 */
class AuthService
{
    private $userResource;
    private $cartResource;
    private $wishlistResource;

    public function __construct()
    {
        $this->userResource = new Resource_User();
        $this->cartResource = new Resource_Cart();
        $this->wishlistResource = new Resource_Wishlist();
    }

    /**
     * Check if user is logged in
     * 
     * @return bool
     */
    public static function check()
    {
        if (session_status() === PHP_SESSION_NONE) {
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
        $user = $this->userResource->findByEmail($email);

        if (!$user) {
            return false;
        }

        if (!password_verify($password, $user['password'])) {
            return false;
        }

        // Set session
        $_SESSION['user_id'] = $user['entity_id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];

        // Merge guest data
        $this->mergeGuestData($user['entity_id']);

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
        // Check if exists
        if ($this->userResource->findByEmail($email)) {
            return false;
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Save returns string ID
        $userId = (int) $this->userResource->save([
            'email' => $email,
            'password' => $hashedPassword,
            'name' => $name,
            'role' => 'customer',
            'is_active' => true,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        if ($userId) {
            // Auto-login
            $_SESSION['user_id'] = $userId;
            $_SESSION['user_name'] = $name;
            $_SESSION['user_email'] = $email;
            $_SESSION['user_role'] = 'customer';

            // Merge guest data
            $this->mergeGuestData($userId);

            return $userId;
        }

        return false;
    }

    /**
     * Logout user
     */
    public function logout()
    {
        // Clear session
        $_SESSION['user_id'] = null;
        unset($_SESSION['cart_id']);
        unset($_SESSION['user_name']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_role']);
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

        return $this->userResource->load($_SESSION['user_id']);
    }

    /**
     * Merge guest data into user account
     */
    private function mergeGuestData($userId)
    {
        // Transfer cart ownership from guest to user
        $sessionId = session_id();
        $guestCart = $this->cartResource->findActive(null, $sessionId);

        if ($guestCart) {
            // Check if user already has an active cart
            $userCart = $this->cartResource->findActive($userId);

            if ($userCart) {
                // Merge items from guest cart to user cart
                $guestItems = $this->cartResource->getItems($guestCart['cart_id']);
                foreach ($guestItems as $pid => $qty) {
                    $this->cartResource->saveItem($userCart['cart_id'], $pid, $qty);
                }

                // Inactivate guest cart
                QueryBuilder::update('sales_cart', ['is_active' => false])
                    ->where('cart_id', '=', $guestCart['cart_id'])
                    ->execute();
            } else {
                // Assign guest cart to user
                QueryBuilder::update('sales_cart', ['customer_id' => $userId, 'session_id' => null])
                    ->where('cart_id', '=', $guestCart['cart_id'])
                    ->execute();
            }
        }

        // Merge Wishlist from session
        if (isset($_SESSION['guest_wishlist']) && !empty($_SESSION['guest_wishlist'])) {
            foreach ($_SESSION['guest_wishlist'] as $pid) { // Helper stores generic array, keys irrelevant
                $this->wishlistResource->add($userId, $pid);
            }
            unset($_SESSION['guest_wishlist']);
        }
    }
}
