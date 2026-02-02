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
        if (!$userId)
            return false; // Or redirect to login

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
        if (!$userId)
            return false;

        $wishlist = $this->wishlistRepo->get($userId);
        return in_array($productId, $wishlist);
    }

    /**
     * Get wishlist count
     * 
     * @return int
     */
    public function getCount()
    {
        $userId = $this->getUserId();
        if (!$userId)
            return 0;

        return count($this->wishlistRepo->get($userId));
    }

    /**
     * Get wishlist contents
     * 
     * @return array
     */
    public function get()
    {
        $userId = $this->getUserId();
        if (!$userId)
            return [];

        return $this->wishlistRepo->get($userId);
    }
}
