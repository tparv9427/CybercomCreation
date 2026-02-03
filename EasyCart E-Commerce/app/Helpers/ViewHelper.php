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

    // ========================================================================
    // XSS Protection & Security Methods
    // ========================================================================

    /**
     * Escape HTML output (XSS protection)
     */
    public static function e(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }

    /**
     * Escape for use in JavaScript
     */
    public static function js($value): string
    {
        return json_encode($value ?? '', JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
    }

    /**
     * Escape URL
     */
    public static function url(?string $value): string
    {
        return rawurlencode($value ?? '');
    }

    // ========================================================================
    // Asset & Route Helpers
    // ========================================================================

    /**
     * Generate asset URL
     */
    public static function asset(string $path): string
    {
        return '/' . ltrim($path, '/');
    }

    /**
     * Generate route URL
     */
    public static function route(string $name, array $params = []): string
    {
        $url = '/' . ltrim($name, '/');

        if (!empty($params)) {
            $url .= '?' . http_build_query($params);
        }

        return $url;
    }

    // ========================================================================
    // Utility Methods
    // ========================================================================

    /**
     * Truncate text to specified length
     */
    public static function truncate(string $text, int $length = 100, string $suffix = '...'): string
    {
        if (mb_strlen($text) <= $length) {
            return $text;
        }

        return mb_substr($text, 0, $length) . $suffix;
    }

    /**
     * Convert newlines to <br> tags (with XSS protection)
     */
    public static function nl2br(?string $text): string
    {
        return nl2br(self::e($text));
    }

    /**
     * Check if current route matches given route
     */
    public static function isActiveRoute(string $route): bool
    {
        $currentUri = $_SERVER['REQUEST_URI'] ?? '';
        $currentPath = parse_url($currentUri, PHP_URL_PATH);

        return $currentPath === '/' . ltrim($route, '/');
    }

    /**
     * Get active class if route matches
     */
    public static function activeClass(string $route, string $activeClass = 'active'): string
    {
        return self::isActiveRoute($route) ? $activeClass : '';
    }
}
