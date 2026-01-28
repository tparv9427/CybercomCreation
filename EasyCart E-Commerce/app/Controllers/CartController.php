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
                
                $cart_items[] = [
                    'product' => $product,
                    'quantity' => $adjusted_quantity,
                    'total' => $product['price'] * $adjusted_quantity
                ];
            }
        }

        $saved_items = $this->cartService->getSavedItems();

        $pricing = $this->pricingService->calculateAll($cart, 'standard');

        $formatPrice = function($price) {
            return \EasyCart\Helpers\FormatHelper::price($price);
        };

        $getCategory = function($id) {
            return $this->categoryRepo->find($id);
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
        $pricing = $this->pricingService->calculateAll($cart, 'standard');
        
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
        $pricing = $this->pricingService->calculateAll($cart, 'standard');

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

    /**
     * Save for later (AJAX)
     */
    /**
     * Save for later (AJAX)
     */
    public function saveForLater()
    {
        header('Content-Type: application/json');
        
        $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

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

        echo json_encode([
            'success' => true,
            'cart_count' => $this->cartService->getCount(),
            'message' => 'Item saved for later',
            'subtotal' => \EasyCart\Helpers\FormatHelper::price($pricing['subtotal']),
            'shipping' => \EasyCart\Helpers\FormatHelper::price($pricing['shipping']),
            'tax' => \EasyCart\Helpers\FormatHelper::price($pricing['tax']),
            'total' => \EasyCart\Helpers\FormatHelper::price($pricing['total']),
            'saved_item_html' => $savedItemHtml
        ]);
    }

    /**
     * Move to cart from saved (AJAX)
     */
    public function moveToCart()
    {
        header('Content-Type: application/json');
        
        $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

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

        echo json_encode([
            'success' => true,
            'cart_count' => $this->cartService->getCount(),
            'message' => 'Item moved back to cart',
            'subtotal' => \EasyCart\Helpers\FormatHelper::price($pricing['subtotal']),
            'shipping' => \EasyCart\Helpers\FormatHelper::price($pricing['shipping']),
            'tax' => \EasyCart\Helpers\FormatHelper::price($pricing['tax']),
            'total' => \EasyCart\Helpers\FormatHelper::price($pricing['total']),
            'cart_item_html' => $cartItemHtml
        ]);
    }

    private function generateSavedItemHtml($item)
    {
        $price = \EasyCart\Helpers\FormatHelper::price($item['product']['price']);
        
        return '
        <div class="cart-item" style="opacity: 0.9;" id="saved-item-' . $item['product']['id'] . '">
            <div class="item-image" onclick="window.location.href=\'product.php?id=' . $item['product']['id'] . '\'">' . $item['product']['icon'] . '</div>
            <div class="item-details">
                <h3 class="item-name">' . $item['product']['name'] . '</h3>
                <p class="item-price">' . $price . '</p>
                <button class="btn btn-sm btn-outline" onclick="moveToCartFromSaved(' . $item['product']['id'] . ')" style="margin-top: 0.5rem;">Move to Cart</button>
            </div>
            <div class="item-total"></div> 
        </div>';
    }

    private function generateCartItemHtml($item)
    {
        $price = \EasyCart\Helpers\FormatHelper::price($item['product']['price']);
        $total = \EasyCart\Helpers\FormatHelper::price($item['total']);
        $category = $this->categoryRepo->find($item['product']['category_id'])['name'];

        return '
        <div class="cart-item" data-product-id="' . $item['product']['id'] . '">
            <div class="item-image" onclick="window.location.href=\'product.php?id=' . $item['product']['id'] . '\'">' . $item['product']['icon'] . '</div>
            <div class="item-details">
                <h3 class="item-name">' . $item['product']['name'] . '</h3>
                <p class="item-category">' . $category . '</p>
                <p class="item-price">' . $price . '</p>
                <button class="btn-link" onclick="saveForLater(' . $item['product']['id'] . ')" style="color: var(--primary); margin-top: 0.5rem; display: block; background: none; border: none; padding: 0; cursor: pointer; text-decoration: underline;">Save for Later</button>
            </div>
            <div class="item-quantity">
                <div class="quantity-controls">
                    <button class="quantity-btn" onclick="decreaseCartQuantity(' . $item['product']['id'] . ')">−</button>
                    <input type="number" class="quantity-input" id="qty-' . $item['product']['id'] . '" value="' . $item['quantity'] . '" min="1" max="' . $item['product']['stock'] . '" oninput="validateCartQuantity(' . $item['product']['id'] . ', this)">
                    <button class="quantity-btn" onclick="increaseCartQuantity(' . $item['product']['id'] . ')">+</button>
                </div>
            </div>
            <div class="item-total">' . $total . '</div>
            <button class="item-remove" onclick="removeFromCart(' . $item['product']['id'] . ')">×</button>
        </div>';
    }
}
