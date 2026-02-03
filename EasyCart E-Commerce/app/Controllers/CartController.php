<?php

namespace EasyCart\Controllers;

use EasyCart\Services\CartService;
use EasyCart\Services\PricingService;
use EasyCart\Repositories\ProductRepository;
use EasyCart\Repositories\CategoryRepository;

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
    private $categoryRepo;

    public function __construct()
    {
        $this->cartService = new CartService();
        $this->pricingService = new PricingService();
        $this->productRepo = new ProductRepository();
        $this->categoryRepo = new CategoryRepository();
    }

    /**
     * Cart page
     */
    public function index()
    {
        $page_title = 'Shopping Cart';
        $categories = $this->categoryRepo->getAll();

        $cart = $this->cartService->get();
        $cart_items = [];

        foreach ($cart as $product_id => $quantity) {
            $product = $this->productRepo->find($product_id);
            if ($product) {
                // Enforce stock limit on display
                $adjusted_quantity = min($quantity, $product['stock']);

                // Determine shipping type for this product
                $shipping_type = ($product['price'] >= 300) ? 'Freight Shipping' : 'Express Shipping';

                $cart_items[] = [
                    'product' => $product,
                    'quantity' => $adjusted_quantity,
                    'total' => $product['price'] * $adjusted_quantity,
                    'shipping_type' => $shipping_type
                ];
            }
        }

        $saved_items = $this->cartService->getSavedItems();

        $pricing = $this->pricingService->calculateAll($cart, 'standard');
        $estimated_totals = $this->pricingService->calculateEstimatedTotalRange($cart);
        $shipping_category = $this->pricingService->determineShippingCategory($cart);

        $formatPrice = [\EasyCart\Helpers\ViewHelper::class, 'formatPrice'];
        $getCategory = [\EasyCart\Helpers\ViewHelper::class, 'getCategory'];

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

        $product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
        $quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;

        $result = $this->cartService->add($product_id, $quantity);

        if ($result['success']) {
            $cart = $this->cartService->get();
            $shipping_category = $this->pricingService->determineShippingCategory($cart);

            $message = 'Product added to cart';
            if ($result['max_stock_reached']) {
                $message = "Maximum stock reached! Only {$result['max_stock']} available.";
            }

            echo json_encode([
                'success' => true,
                'cart_count' => $this->cartService->getCount(),
                'shipping_category' => $shipping_category,
                'message' => $message,
                'max_stock_reached' => $result['max_stock_reached'],
                'current_quantity' => $result['current_quantity'],
                'max_stock' => $result['max_stock']
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

        $product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
        $quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;

        $actual_quantity = $this->cartService->update($product_id, $quantity);
        // If update returned 0 (removed) or false, handle gracefully, but we assume int return now.

        $cart = $this->cartService->get();
        $shipping_category = $this->pricingService->determineShippingCategory($cart);
        $pricing = $this->pricingService->calculateAll($cart, 'standard');

        $product = $this->productRepo->find($product_id);
        $item_total = $product ? $product['price'] * $actual_quantity : 0;
        $max_stock = $product ? (int) $product['stock'] : 0;

        $estimated_totals = $this->pricingService->calculateEstimatedTotalRange($cart);

        echo json_encode([
            'success' => true,
            'cart_count' => $this->cartService->getCount(),
            'actual_quantity' => $actual_quantity,
            'max_stock' => $max_stock,
            'shipping_category' => $shipping_category,
            'item_total' => \EasyCart\Helpers\FormatHelper::price($item_total),
            'subtotal' => \EasyCart\Helpers\FormatHelper::price($pricing['subtotal']),
            'shipping' => \EasyCart\Helpers\FormatHelper::price($pricing['shipping']),
            'tax' => \EasyCart\Helpers\FormatHelper::price($pricing['tax']),
            'item_tax' => \EasyCart\Helpers\FormatHelper::price($estimated_totals['tax_on_items']),
            'total' => \EasyCart\Helpers\FormatHelper::price($pricing['total']),
            'cart_value' => \EasyCart\Helpers\FormatHelper::price($estimated_totals['cart_value']),
            'estimated_total_min' => \EasyCart\Helpers\FormatHelper::price($estimated_totals['min']),
            'estimated_total_max' => \EasyCart\Helpers\FormatHelper::price($estimated_totals['max'])
        ]);
    }

    /**
     * Remove from cart (AJAX)
     */
    public function remove()
    {
        header('Content-Type: application/json');

        $product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;

        $this->cartService->remove($product_id);

        $cart = $this->cartService->get();
        $shipping_category = $this->pricingService->determineShippingCategory($cart);
        $pricing = $this->pricingService->calculateAll($cart, 'standard');

        $estimated_totals = $this->pricingService->calculateEstimatedTotalRange($cart);

        echo json_encode([
            'success' => true,
            'cart_count' => $this->cartService->getCount(),
            'shipping_category' => $shipping_category,
            'message' => 'Product removed from cart',
            'subtotal' => \EasyCart\Helpers\FormatHelper::price($pricing['subtotal']),
            'shipping' => \EasyCart\Helpers\FormatHelper::price($pricing['shipping']),
            'tax' => \EasyCart\Helpers\FormatHelper::price($pricing['tax']),
            'item_tax' => \EasyCart\Helpers\FormatHelper::price($estimated_totals['tax_on_items']),
            'total' => \EasyCart\Helpers\FormatHelper::price($pricing['total']),
            'cart_value' => \EasyCart\Helpers\FormatHelper::price($estimated_totals['cart_value']),
            'estimated_total_min' => \EasyCart\Helpers\FormatHelper::price($estimated_totals['min']),
            'estimated_total_max' => \EasyCart\Helpers\FormatHelper::price($estimated_totals['max'])
        ]);
    }

    /**
     * Save for later (AJAX)
     */
    /**
     * Save for later (AJAX)
     */
    public function saveForLater()
    {
        header('Content-Type: application/json');

        $product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;

        // Perform save logic
        $this->cartService->saveForLater($product_id);

        // Calculate new cart totals
        $cart = $this->cartService->get();
        $pricing = $this->pricingService->calculateAll($cart, 'standard');

        // Generate HTML for the saved item
        $product = $this->productRepo->find($product_id);
        $savedItemHtml = '';
        if ($product) {
            $savedItemHtml = $this->generateSavedItemHtml([
                'product' => $product,
                'quantity' => 1 // Saved items usually handled as single entires or preserve qty? Logic preserves qty but view ignores it currently.
            ]);
        }

        $estimated_totals = $this->pricingService->calculateEstimatedTotalRange($cart);

        echo json_encode([
            'success' => true,
            'cart_count' => $this->cartService->getCount(),
            'message' => 'Item saved for later',
            'subtotal' => \EasyCart\Helpers\FormatHelper::price($pricing['subtotal']),
            'shipping' => \EasyCart\Helpers\FormatHelper::price($pricing['shipping']),
            'tax' => \EasyCart\Helpers\FormatHelper::price($pricing['tax']),
            'item_tax' => \EasyCart\Helpers\FormatHelper::price($estimated_totals['tax_on_items']),
            'total' => \EasyCart\Helpers\FormatHelper::price($pricing['total']),
            'cart_value' => \EasyCart\Helpers\FormatHelper::price($estimated_totals['cart_value']),
            'estimated_total_min' => \EasyCart\Helpers\FormatHelper::price($estimated_totals['min']),
            'estimated_total_max' => \EasyCart\Helpers\FormatHelper::price($estimated_totals['max']),
            'saved_item_html' => $savedItemHtml
        ]);
    }

    /**
     * Move to cart from saved (AJAX)
     */
    public function moveToCart()
    {
        header('Content-Type: application/json');

        $product_id = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;

        $this->cartService->moveToCartFromSaved($product_id);

        $cart = $this->cartService->get();
        $pricing = $this->pricingService->calculateAll($cart, 'standard');

        // Generate HTML for the cart item
        $product = $this->productRepo->find($product_id);
        $cartItemHtml = '';
        if ($product) {
            // We need the quantity from the cart to display correctly
            $quantity = $cart[$product_id] ?? 1;
            $cartItemHtml = $this->generateCartItemHtml([
                'product' => $product,
                'quantity' => $quantity,
                'total' => $product['price'] * $quantity
            ]);
        }

        $estimated_totals = $this->pricingService->calculateEstimatedTotalRange($cart);

        echo json_encode([
            'success' => true,
            'cart_count' => $this->cartService->getCount(),
            'message' => 'Item moved back to cart',
            'subtotal' => \EasyCart\Helpers\FormatHelper::price($pricing['subtotal']),
            'shipping' => \EasyCart\Helpers\FormatHelper::price($pricing['shipping']),
            'tax' => \EasyCart\Helpers\FormatHelper::price($pricing['tax']),
            'item_tax' => \EasyCart\Helpers\FormatHelper::price($estimated_totals['tax_on_items']),
            'total' => \EasyCart\Helpers\FormatHelper::price($pricing['total']),
            'cart_value' => \EasyCart\Helpers\FormatHelper::price($estimated_totals['cart_value']),
            'estimated_total_min' => \EasyCart\Helpers\FormatHelper::price($estimated_totals['min']),
            'estimated_total_max' => \EasyCart\Helpers\FormatHelper::price($estimated_totals['max']),
            'cart_item_html' => $cartItemHtml
        ]);
    }

    private function generateSavedItemHtml($item)
    {
        $item['formatted_price'] = \EasyCart\Helpers\FormatHelper::price($item['product']['price']);
        return \EasyCart\Core\View::render('components/saved_item', $item);
    }

    private function generateCartItemHtml($item)
    {
        $item['category_name'] = $this->categoryRepo->find($item['product']['category_id'])['name'];
        $item['formatted_price'] = \EasyCart\Helpers\FormatHelper::price($item['product']['price']);
        $item['formatted_total'] = \EasyCart\Helpers\FormatHelper::price($item['total']);

        // Ensure shipping_type is set
        if (!isset($item['shipping_type'])) {
            $item['shipping_type'] = ($item['product']['price'] >= 300) ? 'Freight Shipping' : 'Express Shipping';
        }

        return \EasyCart\Core\View::render('components/cart_item', $item);
    }
    /**
     * Get cart count (AJAX)
     */
    public function count()
    {
        header('Content-Type: application/json');
        echo json_encode(['success' => true, 'cart_count' => $this->cartService->getCount()]);
    }
}
