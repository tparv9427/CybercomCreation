<?php
/**
 * Web Routes (New MVC Architecture)
 * 
 * Uses new Controller_ naming convention
 */

use EasyCart\Core\Router;

$router = new Router();

// ============================================================================
// Public Routes (New Controllers)
// ============================================================================

// Home
$router->get('/', ['\\EasyCart\\Controller\\Controller_Home', 'index'], 'home.new');

// Products
$router->get('/products', ['\\EasyCart\\Controller\\Controller_Product', 'index'], 'products.new');
$router->get('/product/{id:\\d+}', ['\\EasyCart\\Controller\\Controller_Product', 'show'], 'product.show.new');
$router->get('/p/{slug}', ['\\EasyCart\\Controller\\Controller_Product', 'showBySlug'], 'product.slug');

// Cart
$router->get('/cart', ['\\EasyCart\\Controller\\Controller_Cart', 'index'], 'cart.new');
$router->post('/cart/add', ['\\EasyCart\\Controller\\Controller_Cart', 'add'], 'cart.add.new', ['csrfProtection']);
$router->post('/cart/update', ['\\EasyCart\\Controller\\Controller_Cart', 'update'], 'cart.update.new', ['csrfProtection']);
$router->post('/cart/remove', ['\\EasyCart\\Controller\\Controller_Cart', 'remove'], 'cart.remove.new', ['csrfProtection']);

return $router;
