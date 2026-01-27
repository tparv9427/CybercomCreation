<?php

namespace EasyCart\Controllers;

use EasyCart\Services\AuthService;
use EasyCart\Services\CartService;
use EasyCart\Services\PricingService;
use EasyCart\Repositories\ProductRepository;

/**
 * CheckoutController
 * 
 * Migrated from: checkout.php
 */
class CheckoutController
{
    private $authService;
    private $cartService;
    private $pricingService;
    private $productRepo;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->cartService = new CartService();
        $this->pricingService = new PricingService();
        $this->productRepo = new ProductRepository();
    }

    /**
     * Checkout page
     */
    public function index()
    {
        // Redirect to login if not logged in
        if (!AuthService::check()) {
            $_SESSION['checkout_redirect'] = true;
            header('Location: login.php');
            exit;
        }

        $page_title = 'Checkout';
        
        $cart = $this->cartService->get();
        $cart_items = [];
        
        foreach ($cart as $product_id => $quantity) {
            $product = $this->productRepo->find($product_id);
            if ($product) {
                $cart_items[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'total' => $product['price'] * $quantity
                ];
            }
        }

        $pricing = $this->pricingService->calculateAll($cart);
        $subtotal = $pricing['subtotal'];
        $shipping = $pricing['shipping'];
        $tax = $pricing['tax'];
        $total = $pricing['total'];
        $total_items = $pricing['item_count'];

        $formatPrice = function($price) {
            return \EasyCart\Helpers\FormatHelper::price($price);
        };

        include __DIR__ . '/../Views/layouts/header.php';
        include __DIR__ . '/../Views/checkout/index.php';
        include __DIR__ . '/../Views/layouts/footer.php';
    }

    /**
     * Process order (POST)
     */
    public function process()
    {
        if (!AuthService::check()) {
            header('Location: login.php');
            exit;
        }

        // Process order
        $order_id = 'ORD-' . strtoupper(substr(md5(time()), 0, 8));
        $_SESSION['last_order_id'] = $order_id;
        
        // Clear cart
        $this->cartService->update(0, 0); // This will clear all items
        
        header('Location: order-success.php');
        exit;
    }
}
