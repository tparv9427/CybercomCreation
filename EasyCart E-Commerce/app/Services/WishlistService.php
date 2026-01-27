<?php

namespace EasyCart\Services;

use EasyCart\Repositories\WishlistRepository;

/**
 * WishlistService
 * 
 * Migrated from: ajax_wishlist.php, config.php (wishlist functions)
 */
class WishlistService
{
    private $wishlistRepo;

    public function __construct()
    {
        $this->wishlistRepo = new WishlistRepository();
    }

    /**
     * Toggle product in wishlist
     * 
     * @param int $productId
     * @return bool True if added, false if removed
     */
    public function toggle($productId)
    {
        $wishlist = $this->wishlistRepo->get();

        if (in_array($productId, $wishlist)) {
            // Remove
            $wishlist = array_diff($wishlist, [$productId]);
            $this->wishlistRepo->save(array_values($wishlist));
            return false;
        } else {
            // Add
            $wishlist[] = $productId;
            $this->wishlistRepo->save($wishlist);
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
        $wishlist = $this->wishlistRepo->get();
        
        if (!in_array($productId, $wishlist)) {
            $wishlist[] = $productId;
            $this->wishlistRepo->save($wishlist);
        }

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
        $wishlist = $this->wishlistRepo->get();
        $wishlist = array_diff($wishlist, [$productId]);
        $this->wishlistRepo->save(array_values($wishlist));

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
        $wishlist = $this->wishlistRepo->get();
        return in_array($productId, $wishlist);
    }

    /**
     * Get wishlist count
     * 
     * @return int
     */
    public function getCount()
    {
        return count($this->wishlistRepo->get());
    }

    /**
     * Get wishlist contents
     * 
     * @return array
     */
    public function get()
    {
        return $this->wishlistRepo->get();
    }
}
