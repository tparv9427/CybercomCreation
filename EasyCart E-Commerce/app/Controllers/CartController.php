<?php

namespace EasyCart\Controllers;

use EasyCart\Services\CartService;
use EasyCart\Services\PricingService;
use EasyCart\Repositories\ProductRepository;

/**
 * CartController
 * 
 * Migrated from: cart.php, ajax_cart.php
 */
class CartController
{
    private $cartService;
    private $pricingService;
    private $productRepo;

    public function __construct()
    {
        $this->cartService = new CartService();
        $this->pricingService = new PricingService();
        $this->productRepo = new ProductRepository();
    }

    /**
     * Cart page
     */
    public function index()
    {
        $page_title = 'Shopping Cart';
        
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

        $formatPrice = function($price) {
            return \EasyCart\Helpers\FormatHelper::price($price);
        };

        include __DIR__ . '/../Views/layouts/header.php';
        include __DIR__ . '/../Views/cart/index.php';
        include __DIR__ . '/../Views/layouts/footer.php';
    }

    /**
     * Add to cart (AJAX)
     */
    public function add()
    {
        header('Content-Type: application/json');
        
        $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

        $success = $this->cartService->add($product_id, $quantity);

        if ($success) {
            echo json_encode([
                'success' => true,
                'cart_count' => $this->cartService->getCount(),
                'message' => 'Product added to cart'
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Product not found']);
        }
    }

    /**
     * Update cart (AJAX)
     */
    public function update()
    {
        header('Content-Type: application/json');
        
        $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

        $this->cartService->update($product_id, $quantity);

        $cart = $this->cartService->get();
        $pricing = $this->pricingService->calculateAll($cart);
        
        $product = $this->productRepo->find($product_id);
        $item_total = $product ? $product['price'] * $quantity : 0;

        echo json_encode([
            'success' => true,
            'cart_count' => $this->cartService->getCount(),
            'item_total' => \EasyCart\Helpers\FormatHelper::price($item_total),
            'subtotal' => \EasyCart\Helpers\FormatHelper::price($pricing['subtotal']),
            'shipping' => \EasyCart\Helpers\FormatHelper::price($pricing['shipping']),
            'tax' => \EasyCart\Helpers\FormatHelper::price($pricing['tax']),
            'total' => \EasyCart\Helpers\FormatHelper::price($pricing['total'])
        ]);
    }

    /**
     * Remove from cart (AJAX)
     */
    public function remove()
    {
        header('Content-Type: application/json');
        
        $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

        $this->cartService->remove($product_id);

        $cart = $this->cartService->get();
        $pricing = $this->pricingService->calculateAll($cart);

        echo json_encode([
            'success' => true,
            'cart_count' => $this->cartService->getCount(),
            'message' => 'Product removed from cart',
            'subtotal' => \EasyCart\Helpers\FormatHelper::price($pricing['subtotal']),
            'shipping' => \EasyCart\Helpers\FormatHelper::price($pricing['shipping']),
            'tax' => \EasyCart\Helpers\FormatHelper::price($pricing['tax']),
            'total' => \EasyCart\Helpers\FormatHelper::price($pricing['total'])
        ]);
    }
}
