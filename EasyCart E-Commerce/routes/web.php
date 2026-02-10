<?php
/**
 * Web Routes
 * 
 * Define all application routes here
 * Updated to use new Controller_* MVC namespace
 */

use EasyCart\Core\Router;

$router = new Router();

// ============================================================================
// Public Routes
// ============================================================================

// Home
$router->get('/', ['\EasyCart\Controller\Controller_Home', 'index'], 'home');

// Products
$router->get('/products', ['\EasyCart\Controller\Controller_Product', 'index'], 'products');
$router->get('/p/{slug}', ['\EasyCart\Controller\Controller_Product', 'showBySlug'], 'product.show.slug');

// Redirect legacy /product/ formats to canonical /p/
$router->get('/product/{id:\d+}', ['\EasyCart\Controller\Controller_Product', 'show'], 'product.show');
$router->get('/product/{slug}', ['\EasyCart\Controller\Controller_Product', 'showBySlug'], 'product.show.slug.legacy');
$router->get('/search', ['\EasyCart\Controller\Controller_Product', 'search'], 'search');
$router->get('/brand/{id}', ['\EasyCart\Controller\Controller_Product', 'brand'], 'brand');

// Cart (GET requests)
$router->get('/cart', ['\EasyCart\Controller\Controller_Cart', 'index'], 'cart');
$router->get('/cart/count', ['\EasyCart\Controller\Controller_Cart', 'count'], 'cart.count');

// Cart (POST requests with CSRF protection)
$router->post('/cart/add', ['\EasyCart\Controller\Controller_Cart', 'add'], 'cart.add', ['csrfProtection']);
$router->post('/cart/update', ['\EasyCart\Controller\Controller_Cart', 'update'], 'cart.update', ['csrfProtection']);
$router->post('/cart/remove', ['\EasyCart\Controller\Controller_Cart', 'remove'], 'cart.remove', ['csrfProtection']);
$router->post('/cart/save-for-later', ['\EasyCart\Controller\Controller_Cart', 'saveForLater'], 'cart.save_later', ['csrfProtection']);
$router->post('/cart/move-to-cart', ['\EasyCart\Controller\Controller_Cart', 'moveToCart'], 'cart.move', ['csrfProtection']);
$router->post('/cart/check-email', ['\EasyCart\Controller\Controller_Cart', 'checkEmail'], 'cart.check_email', ['csrfProtection']);

// Wishlist (GET requests)
$router->get('/wishlist', ['\EasyCart\Controller\Controller_Wishlist', 'index'], 'wishlist');
$router->get('/wishlist/count', ['\EasyCart\Controller\Controller_Wishlist', 'count'], 'wishlist.count');

// Wishlist (POST requests with CSRF protection)
$router->post('/wishlist/toggle', ['\EasyCart\Controller\Controller_Wishlist', 'toggle'], 'wishlist.toggle', ['csrfProtection']);
$router->post('/wishlist/move', ['\EasyCart\Controller\Controller_Wishlist', 'moveToCart'], 'wishlist.move', ['csrfProtection', 'authRequired']);

// ============================================================================
// Authentication Routes
// ============================================================================

// Login (guest only)
$router->get('/login', ['\EasyCart\Controller\Controller_Auth', 'showLogin'], 'login', ['guestOnly']);
$router->post('/login', ['\EasyCart\Controller\Controller_Auth', 'login'], 'login.post', ['csrfProtection', 'guestOnly']);

// Signup (guest only)
$router->get('/signup', ['\EasyCart\Controller\Controller_Auth', 'showSignup'], 'signup', ['guestOnly']);
$router->post('/signup', ['\EasyCart\Controller\Controller_Auth', 'signup'], 'signup.post', ['csrfProtection', 'guestOnly']);

// Logout (auth required)
$router->get('/logout', ['\EasyCart\Controller\Controller_Auth', 'logout'], 'logout', ['authRequired', 'checkActiveUser']);

// ============================================================================
// Admin Routes (Quick & Dirty)
// ============================================================================
$router->get('/admin/import-export', ['\EasyCart\Controller\Controller_Admin', 'importExport'], 'admin.import_export', ['authRequired']);
$router->get('/admin/export', ['\EasyCart\Controller\Controller_Admin', 'export'], 'admin.export', ['authRequired']);
$router->post('/admin/import', ['\EasyCart\Controller\Controller_Admin', 'import'], 'admin.import', ['authRequired', 'csrfProtection']);

// ============================================================================
// Protected Routes (Require Authentication)
// ============================================================================

// Checkout
$router->get('/checkout', ['\EasyCart\Controller\Controller_Checkout', 'index'], 'checkout', ['authRequired', 'checkActiveUser']);
$router->post('/checkout', ['\EasyCart\Controller\Controller_Checkout', 'process'], 'checkout.process', ['csrfProtection', 'authRequired', 'checkActiveUser']);
$router->post('/checkout/coupon', ['\EasyCart\Controller\Controller_Checkout', 'coupon'], 'checkout.coupon', ['csrfProtection']);
$router->post('/checkout/pricing', ['\EasyCart\Controller\Controller_Checkout', 'pricing'], 'checkout.pricing', ['csrfProtection']);

// Orders
$router->get('/orders', ['\EasyCart\Controller\Controller_Order', 'index'], 'orders', ['authRequired', 'checkActiveUser']);
$router->get('/order/success', ['\EasyCart\Controller\Controller_Order', 'success'], 'order.success', ['authRequired', 'checkActiveUser']);
$router->get('/order/{id}', ['\EasyCart\Controller\Controller_Order', 'show'], 'order.show', ['authRequired', 'checkActiveUser']);
$router->get('/order/invoice/{id}', ['\EasyCart\Controller\Controller_Order', 'invoice'], 'order.invoice', ['authRequired', 'checkActiveUser']);
$router->get('/order/archive/{id}', ['\EasyCart\Controller\Controller_Order', 'archive'], 'order.archive', ['authRequired', 'checkActiveUser']);

// Dashboard
$router->get('/dashboard', ['\EasyCart\Controller\Controller_Dashboard', 'index'], 'dashboard', ['authRequired', 'checkActiveUser']);
$router->get('/api/dashboard/chart', ['\EasyCart\Controller\Controller_Dashboard', 'chartData'], 'dashboard.chart', ['authRequired', 'checkActiveUser']);

// ============================================================================
// AJAX Routes (for backward compatibility during transition)
// ============================================================================

// These routes handle AJAX requests that may not have been updated yet
$router->post('/ajax/cart', function () {
    $controller = new \EasyCart\Controller\Controller_Cart();
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
    $controller = new \EasyCart\Controller\Controller_Wishlist();
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
