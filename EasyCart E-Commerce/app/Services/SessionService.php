<?php

namespace EasyCart\Services;

use EasyCart\Repositories\CartRepository;
use EasyCart\Repositories\WishlistRepository;

/**
 * SessionService
 * 
 * Migrated from: includes/session-manager.php
 */
class SessionService
{
    /**
     * Initialize session
     */
    public static function init()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Initialize user_id if not set
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['user_id'] = null;
        }
    }

    /**
     * Merge guest data into user account on login
     * 
     * @param int $userId
     */
    public static function mergeGuestData($userId)
    {
        $cartRepo = new CartRepository();
        $wishlistRepo = new WishlistRepository();

        // Load existing user data from disk
        $userCart = $cartRepo->loadFromDisk($userId);
        $userWishlist = $wishlistRepo->loadFromDisk($userId);

        // Merge guest cart
        if (isset($_SESSION['guest_cart']) && !empty($_SESSION['guest_cart'])) {
            foreach ($_SESSION['guest_cart'] as $pid => $qty) {
                if (isset($userCart[$pid])) {
                    $userCart[$pid] += $qty;
                } else {
                    $userCart[$pid] = $qty;
                }
            }
            unset($_SESSION['guest_cart']);
        }

        // Merge guest wishlist
        if (isset($_SESSION['guest_wishlist']) && !empty($_SESSION['guest_wishlist'])) {
            foreach ($_SESSION['guest_wishlist'] as $pid => $val) {
                $userWishlist[$pid] = $val;
            }
            unset($_SESSION['guest_wishlist']);
        }

        // Save merged data
        $_SESSION['cart'] = $userCart;
        $_SESSION['wishlist'] = $userWishlist;
        $cartRepo->saveToDisk($userId, $userCart);
        $wishlistRepo->saveToDisk($userId, $userWishlist);
    }
}
