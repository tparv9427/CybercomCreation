<?php

namespace EasyCart\Helpers;

use EasyCart\Repositories\CategoryRepository;
use EasyCart\Repositories\BrandRepository;
use EasyCart\Services\WishlistService;

class ViewHelper
{
    private static $categoryRepo = null;
    private static $brandRepo = null;
    private static $wishlistService = null;

    /**
     * Get a category by ID
     */
    public static function getCategory($id)
    {
        if (self::$categoryRepo === null) {
            self::$categoryRepo = new CategoryRepository();
        }
        return self::$categoryRepo->find($id);
    }

    /**
     * Get a brand by ID
     */
    public static function getBrand($id)
    {
        if (self::$brandRepo === null) {
            self::$brandRepo = new BrandRepository();
        }
        return self::$brandRepo->find($id);
    }

    /**
     * Check if product is in wishlist
     */
    public static function isInWishlist($productId)
    {
        if (self::$wishlistService === null) {
            self::$wishlistService = new WishlistService();
        }
        return self::$wishlistService->has($productId);
    }

    /**
     * Format price
     */
    public static function formatPrice($price)
    {
        return FormatHelper::price($price);
    }
}
