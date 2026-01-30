<?php
/**
 * Front Controller - public/index.php
 * 
 * Single entry point for all requests.
 * Routes requests to appropriate controllers.
 */

// Load autoloader (Composer or manual)
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
} else {
    require_once __DIR__ . '/../config/autoload.php';
}

// Load configuration
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/constants.php';

// Initialize session
use EasyCart\Services\SessionService;
SessionService::init();

// Helper functions for backward compatibility with views
function getCartCount()
{
    $cartService = new \EasyCart\Services\CartService();
    return $cartService->getCount();
}

function getWishlistCount()
{
    $wishlistService = new \EasyCart\Services\WishlistService();
    return $wishlistService->getCount();
}

function isLoggedIn()
{
    return \EasyCart\Services\AuthService::check();
}

function getCategory($id)
{
    $repo = new \EasyCart\Repositories\CategoryRepository();
    return $repo->find($id);
}

function getBrand($id)
{
    $repo = new \EasyCart\Repositories\BrandRepository();
    return $repo->find($id);
}

function getBrands()
{
    $repo = new \EasyCart\Repositories\BrandRepository();
    return $repo->getAll();
}

function formatPrice($price)
{
    return \EasyCart\Helpers\FormatHelper::price($price);
}

function isInCart($productId)
{
    $cartService = new \EasyCart\Services\CartService();
    return $cartService->has($productId);
}

function isInWishlist($productId)
{
    $wishlistService = new \EasyCart\Services\WishlistService();
    return $wishlistService->has($productId);
}

// Simple routing
$route = $_GET['route'] ?? '';

// Extract route from URL if not specified
if (empty($route)) {
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';

    if (strpos($requestUri, 'products.php') !== false) {
        $route = 'products';
    } elseif (strpos($requestUri, 'product.php') !== false) {
        $route = 'product';
    } elseif (strpos($requestUri, 'cart.php') !== false) {
        $route = 'cart';
    } elseif (strpos($requestUri, 'checkout.php/pricing') !== false) {
        $route = 'checkout-pricing';
    } elseif (strpos($requestUri, 'checkout.php') !== false) {
        $route = 'checkout';
    } elseif (strpos($requestUri, 'login.php') !== false) {
        $route = 'login';
    } elseif (strpos($requestUri, 'signup.php') !== false) {
        $route = 'signup';
    } elseif (strpos($requestUri, 'logout.php') !== false) {
        $route = 'logout';
    } elseif (strpos($requestUri, 'wishlist.php') !== false) {
        $route = 'wishlist';
    } elseif (strpos($requestUri, 'orders.php') !== false) {
        $route = 'orders';
    } elseif (strpos($requestUri, 'order-success.php') !== false) {
        $route = 'order-success';
    } elseif (strpos($requestUri, 'search.php') !== false) {
        $route = 'search';
    } elseif (strpos($requestUri, 'brand.php') !== false) {
        $route = 'brand';
    } elseif (strpos($requestUri, 'ajax_cart.php') !== false) {
        $route = 'ajax/cart';
    } elseif (strpos($requestUri, 'ajax_wishlist.php') !== false) {
        $route = 'ajax/wishlist';
    } elseif (strpos($requestUri, 'ajax_checkout_pricing.php') !== false) {
        $route = 'checkout-pricing';
    } else {
        $route = 'home';
    }
}

// Route to controller
try {
    switch ($route) {
        case 'home':
        case '':
            $controller = new \EasyCart\Controllers\HomeController();
            $controller->index();
            break;

        case 'products':
            $controller = new \EasyCart\Controllers\ProductController();
            $controller->index();
            break;

        case 'product':
            $controller = new \EasyCart\Controllers\ProductController();
            $id = $_GET['id'] ?? null;
            $controller->show($id);
            break;

        case 'cart':
            $controller = new \EasyCart\Controllers\CartController();
            $controller->index();
            break;

        case 'ajax/cart':
            $controller = new \EasyCart\Controllers\CartController();
            $action = $_POST['action'] ?? '';
            switch ($action) {
                case 'add':
                    $controller->add();
                    break;
                case 'update':
                    $controller->update();
                    break;
                case 'remove':
                    $controller->remove();
                    break;
                case 'save_for_later':
                    $controller->saveForLater();
                    break;
                case 'move_to_cart':
                    $controller->moveToCart();
                    break;
                case 'count':
                    $controller->count();
                    break;
                default:
                    http_response_code(400);
                    echo json_encode(['success' => false, 'message' => 'Invalid action']);
            }
            break;

        case 'checkout-pricing':
            $controller = new \EasyCart\Controllers\CheckoutController();
            $controller->pricing();
            break;

        case 'checkout':
            $controller = new \EasyCart\Controllers\CheckoutController();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->process();
            } else {
                $controller->index();
            }
            break;

        case 'login':
            $controller = new \EasyCart\Controllers\AuthController();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->login();
            } else {
                $controller->showLogin();
            }
            break;

        case 'signup':
            $controller = new \EasyCart\Controllers\AuthController();
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $controller->signup();
            } else {
                $controller->showSignup();
            }
            break;

        case 'logout':
            $controller = new \EasyCart\Controllers\AuthController();
            $controller->logout();
            break;

        case 'wishlist':
            $controller = new \EasyCart\Controllers\WishlistController();
            $controller->index();
            break;

        case 'ajax/wishlist':
            $controller = new \EasyCart\Controllers\WishlistController();
            $action = $_POST['action'] ?? 'toggle';
            switch ($action) {
                case 'move':
                    $controller->moveToCart();
                    break;
                case 'count':
                    $controller->count();
                    break;
                default:
                    $controller->toggle();
            }
            break;

        case 'orders':
            $controller = new \EasyCart\Controllers\OrderController();
            $controller->index();
            break;

        case 'order-success':
            $controller = new \EasyCart\Controllers\OrderController();
            $controller->success();
            break;

        case 'search':
            $controller = new \EasyCart\Controllers\ProductController();
            $controller->search();
            break;

        case 'brand':
            $controller = new \EasyCart\Controllers\ProductController();
            $id = $_GET['id'] ?? null;
            $controller->brand($id);
            break;

        default:
            http_response_code(404);
            echo '<h1>404 - Page Not Found</h1>';
            break;
    }
} catch (\Exception $e) {
    http_response_code(500);
    error_log($e->getMessage());
    echo '<h1>500 - Internal Server Error</h1>';
    if (defined('DEBUG') && DEBUG) {
        echo '<pre>' . $e->getMessage() . '</pre>';
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
    }
}
