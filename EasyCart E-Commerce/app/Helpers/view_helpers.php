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
