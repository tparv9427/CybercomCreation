<?php
/**
 * Global View Helper Functions
 * 
 * These functions are available globally in all views for convenience
 */

use EasyCart\Helpers\ViewHelper;
use EasyCart\Core\CSRF;

/**
 * Escape HTML output (XSS protection)
 */
function e(?string $value): string
{
    return ViewHelper::e($value);
}

/**
 * Generate asset URL
 */
function asset(string $path): string
{
    return ViewHelper::asset($path);
}

/**
 * Generate route URL
 */
function route(string $name, array $params = []): string
{
    return ViewHelper::route($name, $params);
}

/**
 * Get CSRF token field for forms
 */
function csrf_field(): string
{
    return CSRF::getTokenField();
}

/**
 * Get CSRF token value (for AJAX)
 */
function csrf_token(): string
{
    return CSRF::generateToken();
}

/**
 * Format price
 */
function price($amount): string
{
    return ViewHelper::formatPrice($amount);
}

/**
 * Truncate text
 */
function truncate(string $text, int $length = 100): string
{
    return ViewHelper::truncate($text, $length);
}

/**
 * Check if route is active
 */
function is_active(string $route): bool
{
    return ViewHelper::isActiveRoute($route);
}

/**
 * Get active class if route matches
 */
function active_class(string $route, string $class = 'active'): string
{
    return ViewHelper::activeClass($route, $class);
}
/**
 * Get category by ID
 */
function getCategory(?int $id): ?array
{
    if (!$id)
        return null;
    return ViewHelper::getCategory($id);
}

/**
 * Check if product is in wishlist
 */
function isInWishlist(?int $productId): bool
{
    if (!$productId)
        return false;
    return ViewHelper::isInWishlist($productId);
}

/**
 * Get brand by ID
 */
function getBrand(?int $id): ?array
{
    if (!$id)
        return null;
    return ViewHelper::getBrand($id);
}

/**
 * Format price (alias for price)
 */
function formatPrice($amount): string
{
    return ViewHelper::formatPrice($amount);
}
/**
 * Get item count in cart
 */
function getCartCount(): int
{
    $cartService = new \EasyCart\Services\CartService();
    return $cartService->getCount();
}

/**
 * Get item count in wishlist
 */
function getWishlistCount(): int
{
    $wishlistService = new \EasyCart\Services\WishlistService();
    return $wishlistService->getCount();
}

/**
 * Check if user is logged in
 */
function isLoggedIn(): bool
{
    return \EasyCart\Services\AuthService::check();
}

/**
 * Get all active categories
 */
function getCategories(): array
{
    $resource = new \EasyCart\Resource\Resource_Category();
    return $resource->getAllCategories();
}

/**
 * Get all brands
 */
function getBrands(): array
{
    $resource = new \EasyCart\Resource\Resource_Brand();
    return $resource->getAllBrands();
}

/**
 * Check if product is in cart
 */
function isInCart(?int $productId): bool
{
    if (!$productId)
        return false;
    $cartService = new \EasyCart\Services\CartService();
    return $cartService->has($productId);
}
/**
 * Generate product URL (prefers slug with /p/ prefix)
 */
function product_url(?array $product): string
{
    if (!$product)
        return '#';

    static $rewriteService = null;
    if ($rewriteService === null) {
        $rewriteService = new \EasyCart\Services\UrlRewriteService();
    }

    return $rewriteService->getProductUrl($product);
}
