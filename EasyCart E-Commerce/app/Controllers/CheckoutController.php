<?php

namespace EasyCart\Controllers;

use EasyCart\Services\AuthService;
use EasyCart\Services\CartService;
use EasyCart\Services\PricingService;
use EasyCart\Repositories\ProductRepository;
use EasyCart\Repositories\CategoryRepository;

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
    private $categoryRepo;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->cartService = new CartService();
        $this->pricingService = new PricingService();
        $this->productRepo = new ProductRepository();
        $this->categoryRepo = new CategoryRepository();
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
        $categories = $this->categoryRepo->getAll();
        
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

        $pricing = $this->pricingService->calculateAll($cart, 'standard');
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
     * Get updated pricing (AJAX)
     */
    public function pricing()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            exit;
        }

        $shippingMethod = $_POST['shipping'] ?? 'standard';
        $cart = $this->cartService->get();
        
        $pricing = $this->pricingService->calculateAll($cart, $shippingMethod);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'pricing' => [
                'subtotal' => \EasyCart\Helpers\FormatHelper::price($pricing['subtotal']),
                'shipping' => \EasyCart\Helpers\FormatHelper::price($pricing['shipping']),
                'tax' => \EasyCart\Helpers\FormatHelper::price($pricing['tax']),
                'total' => \EasyCart\Helpers\FormatHelper::price($pricing['total'])
            ]
        ]);
        exit;
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

        // Simulating order processing
        $order_id = 'ORD-' . strtoupper(substr(md5(time()), 0, 8));
        $_SESSION['last_order_id'] = $order_id;
        
        // Clear cart
        $this->cartService->empty(); 
        
        header('Location: order-success.php');
        exit;
    }
}
