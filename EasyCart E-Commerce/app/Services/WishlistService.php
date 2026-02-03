<?php

namespace EasyCart\Services;

use EasyCart\Repositories\WishlistRepository;
use EasyCart\Services\AuthService;

/**
 * WishlistService
 * 
 * Handles business logic for wishlists.
 * Now database-driven, requiring a logged-in user.
 */
class WishlistService
{
    private $wishlistRepo;

    public function __construct()
    {
        $this->wishlistRepo = new WishlistRepository();
    }

    private function getUserId()
    {
        // Ensure session is started
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Toggle product in wishlist
     * 
     * @param int $productId
     * @return bool True if added, false if removed
     */
    public function toggle($productId)
    {
        $userId = $this->getUserId();

        if ($userId) {
            // Logged-in user: use database
            $wishlist = $this->wishlistRepo->get($userId);

            if (in_array($productId, $wishlist)) {
                // Remove
                $this->wishlistRepo->remove($userId, $productId);
                return false;
            } else {
                // Add
                $this->wishlistRepo->add($userId, $productId);
                return true;
            }
        } else {
            // Guest user: use session
            if (!isset($_SESSION['guest_wishlist'])) {
                $_SESSION['guest_wishlist'] = [];
            }

            $key = array_search($productId, $_SESSION['guest_wishlist']);
            if ($key !== false) {
                // Remove
                unset($_SESSION['guest_wishlist'][$key]);
                $_SESSION['guest_wishlist'] = array_values($_SESSION['guest_wishlist']); // Re-index
                return false;
            } else {
                // Add
                $_SESSION['guest_wishlist'][] = $productId;
                return true;
            }
        }
    }

    /**
     * Add product to wishlist
     * 
     * @param int $productId
     * @return bool
     */
    public function add($productId)
    {
        $userId = $this->getUserId();
        if (!$userId)
            return false;

        $this->wishlistRepo->add($userId, $productId);
        return true;
    }

    /**
     * Remove product from wishlist
     * 
     * @param int $productId
     * @return bool
     */
    public function remove($productId)
    {
        $userId = $this->getUserId();
        if (!$userId)
            return false;

        $this->wishlistRepo->remove($userId, $productId);
        return true;
    }

    /**
     * Check if product is in wishlist
     * 
     * @param int $productId
     * @return bool
     */
    public function has($productId)
    {
        $userId = $this->getUserId();

        if ($userId) {
            // Logged-in user: check database
            $wishlist = $this->wishlistRepo->get($userId);
            return in_array($productId, $wishlist);
        } else {
            // Guest user: check session
            return isset($_SESSION['guest_wishlist']) && in_array($productId, $_SESSION['guest_wishlist']);
        }
    }

    /**
     * Get wishlist count
     * 
     * @return int
     */
    public function getCount()
    {
        $userId = $this->getUserId();

        if ($userId) {
            return count($this->wishlistRepo->get($userId));
        } else {
            return isset($_SESSION['guest_wishlist']) ? count($_SESSION['guest_wishlist']) : 0;
        }
    }

    /**
     * Get wishlist contents
     * 
     * @return array
     */
    public function get()
    {
        $userId = $this->getUserId();

        if ($userId) {
            return $this->wishlistRepo->get($userId);
        } else {
            return $_SESSION['guest_wishlist'] ?? [];
        }
    }
}
