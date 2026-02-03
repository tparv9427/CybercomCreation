<?php
/**
 * Web Routes
 * 
 * Define all application routes here
 */

use EasyCart\Core\Router;

$router = new Router();

// ============================================================================
// Public Routes
// ============================================================================

// Home
$router->get('/', ['\EasyCart\Controllers\HomeController', 'index'], 'home');

// Products
$router->get('/products', ['\EasyCart\Controllers\ProductController', 'index'], 'products');
$router->get('/product/{id:\d+}', ['\EasyCart\Controllers\ProductController', 'show'], 'product.show');
$router->get('/search', ['\EasyCart\Controllers\ProductController', 'search'], 'search');
$router->get('/brand/{id}', ['\EasyCart\Controllers\ProductController', 'brand'], 'brand');

// Cart (GET requests)
$router->get('/cart', ['\EasyCart\Controllers\CartController', 'index'], 'cart');
$router->get('/cart/count', ['\EasyCart\Controllers\CartController', 'count'], 'cart.count');

// Cart (POST requests with CSRF protection)
$router->post('/cart/add', ['\EasyCart\Controllers\CartController', 'add'], 'cart.add', ['csrfProtection']);
$router->post('/cart/update', ['\EasyCart\Controllers\CartController', 'update'], 'cart.update', ['csrfProtection']);
$router->post('/cart/remove', ['\EasyCart\Controllers\CartController', 'remove'], 'cart.remove', ['csrfProtection']);
$router->post('/cart/save-for-later', ['\EasyCart\Controllers\CartController', 'saveForLater'], 'cart.save_later', ['csrfProtection']);
$router->post('/cart/move-to-cart', ['\EasyCart\Controllers\CartController', 'moveToCart'], 'cart.move', ['csrfProtection']);

// Wishlist (GET requests)
$router->get('/wishlist', ['\EasyCart\Controllers\WishlistController', 'index'], 'wishlist');
$router->get('/wishlist/count', ['\EasyCart\Controllers\WishlistController', 'count'], 'wishlist.count');

// Wishlist (POST requests with CSRF protection)
$router->post('/wishlist/toggle', ['\EasyCart\Controllers\WishlistController', 'toggle'], 'wishlist.toggle', ['csrfProtection']);
$router->post('/wishlist/move', ['\EasyCart\Controllers\WishlistController', 'moveToCart'], 'wishlist.move', ['csrfProtection', 'authRequired']);

// ============================================================================
// Authentication Routes
// ============================================================================

// Login (guest only)
$router->get('/login', ['\EasyCart\Controllers\AuthController', 'showLogin'], 'login', ['guestOnly']);
$router->post('/login', ['\EasyCart\Controllers\AuthController', 'login'], 'login.post', ['csrfProtection', 'guestOnly']);

// Signup (guest only)
$router->get('/signup', ['\EasyCart\Controllers\AuthController', 'showSignup'], 'signup', ['guestOnly']);
$router->post('/signup', ['\EasyCart\Controllers\AuthController', 'signup'], 'signup.post', ['csrfProtection', 'guestOnly']);

// Logout (auth required)
$router->get('/logout', ['\EasyCart\Controllers\AuthController', 'logout'], 'logout', ['authRequired']);

// ============================================================================
// Protected Routes (Require Authentication)
// ============================================================================

// Checkout
$router->get('/checkout', ['\EasyCart\Controllers\CheckoutController', 'index'], 'checkout', ['authRequired']);
$router->post('/checkout', ['\EasyCart\Controllers\CheckoutController', 'process'], 'checkout.process', ['csrfProtection', 'authRequired']);
$router->post('/checkout/pricing', ['\EasyCart\Controllers\CheckoutController', 'pricing'], 'checkout.pricing', ['csrfProtection']);

// Orders
$router->get('/orders', ['\EasyCart\Controllers\OrderController', 'index'], 'orders', ['authRequired']);
$router->get('/order/success', ['\EasyCart\Controllers\OrderController', 'success'], 'order.success', ['authRequired']);

// ============================================================================
// AJAX Routes (for backward compatibility during transition)
// ============================================================================

// These routes handle AJAX requests that may not have been updated yet
$router->post('/ajax/cart', function () {
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
}, 'ajax.cart', ['csrfProtection']);

$router->post('/ajax/wishlist', function () {
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
}, 'ajax.wishlist', ['csrfProtection']);

return $router;
