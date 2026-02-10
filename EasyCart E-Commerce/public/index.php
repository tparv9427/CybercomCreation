<?php
/**
 * Front Controller - public/index.php
 * 
 * Single entry point for all requests.
 * Uses modern routing system with security middleware.
 */

// ============================================================================
// Autoloader
// ============================================================================
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    require_once __DIR__ . '/../config/autoload.php';
}

// ============================================================================
// Configuration
// ============================================================================
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/constants.php';

// ============================================================================
// Initialize Session
// ============================================================================
use EasyCart\Core\Session;
Session::init();

// ============================================================================
// Load Global View Helpers
// ============================================================================
require_once __DIR__ . '/../app/Helpers/view_helpers.php';

// ============================================================================
// Load Routes
// ============================================================================
$router = require __DIR__ . '/../routes/web.php';

// ============================================================================
// Global Middleware: Coupon Cleanup
// ============================================================================
if (class_exists('\EasyCart\Services\CouponService')) {
    $couponService = new \EasyCart\Services\CouponService();
    $currentUri = $_SERVER['REQUEST_URI'] ?? '';
    $currentPath = parse_url($currentUri, PHP_URL_PATH);

    // Determine route from path
    if (strpos($currentPath, '/checkout') !== false) {
        $route = 'checkout';
    } else {
        $route = 'other';
    }

    $couponService->clearIfNavigatedAway($route);
}

// ============================================================================
// Dispatch Request
// ============================================================================
try {
    $method = $_SERVER['REQUEST_METHOD'];
    $uri = $_SERVER['REQUEST_URI'];

    $router->dispatch($method, $uri);

} catch (\Exception $e) {
    // Log error
    error_log($e->getMessage());
    error_log($e->getTraceAsString());

    // Show error page
    http_response_code(500);

    if (defined('DEBUG') && DEBUG) {
        echo '<h1>500 - Internal Server Error</h1>';
        echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        echo '<pre>' . htmlspecialchars($e->getTraceAsString()) . '</pre>';
    } else {
        echo '<h1>500 - Internal Server Error</h1>';
        echo '<p>Something went wrong. Please try again later.</p>';
    }
}
