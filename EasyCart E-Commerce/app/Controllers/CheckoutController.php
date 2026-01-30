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
    /**
     * Checkout page
     */
    public function index()
    {
        // Redirect to login if not logged in
        if (!AuthService::check()) {
            $_SESSION['checkout_redirect'] = true;
            // Preserve buy now params if present
            if (isset($_GET['action']) && $_GET['action'] === 'buynow') {
                $_SESSION['pending_buynow'] = [
                    'id' => $_GET['id'],
                    'quantity' => $_GET['quantity']
                ];
            }
            header('Location: login.php');
            exit;
        }

        // Restore pending buy now if returning from login
        if (isset($_SESSION['pending_buynow'])) {
            $pending = $_SESSION['pending_buynow'];
            unset($_SESSION['pending_buynow']);
            // Redirect to self with params to properly set state
            header("Location: checkout.php?action=buynow&id={$pending['id']}&quantity={$pending['quantity']}");
            exit;
        }

        // Handle Cancel Action
        if (isset($_GET['action']) && $_GET['action'] === 'cancel') {
            $this->cancel();
        }

        $page_title = 'Checkout';
        $categories = $this->categoryRepo->getAll();

        // Handle Buy Now Mode
        $isBuyNow = isset($_GET['action']) && $_GET['action'] === 'buynow';

        if ($isBuyNow && isset($_GET['id'])) {
            $productId = (int) $_GET['id'];
            $quantity = isset($_GET['quantity']) ? (int) $_GET['quantity'] : 1;

            // Validate stock/product exists
            $product = $this->productRepo->find($productId);
            if ($product) {
                // Set temporary session for buy now
                $_SESSION['buynow_cart'] = [$productId => $quantity];
                $cart = [$productId => $quantity];
            } else {
                header('Location: index.php'); // Invalid product
                exit;
            }
        } else {
            // Normal Checkout - Clear any previous buy now session
            if (isset($_SESSION['buynow_cart'])) {
                unset($_SESSION['buynow_cart']);
            }
            $cart = $this->cartService->get();
        }

        // Redirect if empty (only for normal cart)
        if (empty($cart) && !$isBuyNow) {
            header('Location: index.php');
            exit;
        }

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

        // Determine shipping category
        $shipping_category = $this->pricingService->determineShippingCategory($cart);
        $allowed_methods = $this->pricingService->getAllowedShippingMethods($shipping_category);

        // Get or set shipping method
        $shipping_method = $_SESSION['shipping_method'] ?? 'standard';

        // Validate shipping method against category
        if (!in_array($shipping_method, $allowed_methods)) {
            // Reset to first allowed method if current selection is invalid
            $shipping_method = $allowed_methods[0];
            $_SESSION['shipping_method'] = $shipping_method;
        }

        // Get or set payment method
        $payment_method = $_SESSION['payment_method'] ?? 'card';

        $pricing = $this->pricingService->calculateAll($cart, $shipping_method, $payment_method);
        $subtotal = $pricing['subtotal'];
        $shipping = $pricing['shipping'];
        $payment_fee = $pricing['payment_fee'];
        $tax = $pricing['tax'];
        $total = $pricing['total'];
        $total_items = $pricing['item_count'];
        $allowed_category = $shipping_category;

        $formatPrice = function ($price) {
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
        $paymentMethod = $_POST['payment'] ?? 'card';

        $_SESSION['shipping_method'] = $shippingMethod; // Persist selection
        $_SESSION['payment_method'] = $paymentMethod; // Persist payment selection

        // Check if we are in Buy Now mode
        $isBuyNow = isset($_SESSION['buynow_cart']);
        if ($isBuyNow) {
            $cart = $_SESSION['buynow_cart'];
        } else {
            $cart = $this->cartService->get();
        }

        $pricing = $this->pricingService->calculateAll($cart, $shippingMethod, $paymentMethod);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'debug' => [
                'is_buynow' => $isBuyNow,
                'cart_items' => count($cart),
                'cart_data' => $cart
            ],
            'pricing' => [
                'subtotal' => \EasyCart\Helpers\FormatHelper::price($pricing['subtotal']),
                'shipping' => \EasyCart\Helpers\FormatHelper::price($pricing['shipping']),
                'payment_fee' => $pricing['payment_fee'] > 0 ? \EasyCart\Helpers\FormatHelper::price($pricing['payment_fee']) : null,
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

        $user = $this->authService->getCurrentUser();
        $userId = $user['id'];

        // Determine source of items (Buy Now vs Regular Cart)
        if (isset($_SESSION['buynow_cart'])) {
            $cart = $_SESSION['buynow_cart'];
            $isBuyNowOrder = true;
        } else {
            $cart = $this->cartService->get();
            $isBuyNowOrder = false;
        }

        if (empty($cart)) {
            header('Location: index.php'); // Redirect if cart is empty
            exit;
        }

        // Validate shipping method against category
        $shipping_category = $this->pricingService->determineShippingCategory($cart);
        $allowed_methods = $this->pricingService->getAllowedShippingMethods($shipping_category);

        $shipping_method = $_SESSION['shipping_method'] ?? 'standard';

        // Ensure selected method is allowed for this category
        if (!in_array($shipping_method, $allowed_methods)) {
            $shipping_method = $allowed_methods[0];
            $_SESSION['shipping_method'] = $shipping_method;
        }

        $pricing = $this->pricingService->calculateAll($cart, $shipping_method);

        // Prepare order items
        $orderItems = [];
        foreach ($cart as $product_id => $quantity) {
            $product = $this->productRepo->find($product_id);
            if ($product) {
                $orderItems[] = [
                    'product_id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'image' => $product['image'], // Assuming product has an image field
                    'quantity' => $quantity,
                    'total' => $product['price'] * $quantity
                ];
            }
        }

        // Create order
        $order_id = 'ORD-' . strtoupper(substr(md5(time() . rand()), 0, 8));

        $order = [
            'id' => $order_id,
            'user_id' => $userId,
            'date' => date('F j, Y'),
            'items' => $orderItems,
            'subtotal' => $pricing['subtotal'],
            'shipping' => $pricing['shipping'],
            'tax' => $pricing['tax'],
            'total' => $pricing['total'],
            'status' => 'Processing', // Default status
            'shipping_method' => $shipping_method
        ];

        // Store order in session (simulating database)
        if (!isset($_SESSION['orders'])) {
            $_SESSION['orders'] = [];
        }
        if (!isset($_SESSION['orders'][$userId])) {
            $_SESSION['orders'][$userId] = [];
        }

        // Prepend new order so it shows at top
        array_unshift($_SESSION['orders'][$userId], $order);

        $_SESSION['last_order_id'] = $order_id;

        // Clear cart ONLY if it's a regular order
        if (!$isBuyNowOrder) {
            $this->cartService->empty();
        } else {
            // If Buy Now, just clear the temp session
            unset($_SESSION['buynow_cart']);
        }

        header('Location: order-success.php');
        exit;
    }

    /**
     * Cancel checkout
     */
    private function cancel()
    {
        // If coming from "Buy Now", move items back to main cart
        if (isset($_SESSION['buynow_cart'])) {
            // Add items from buynow_cart to main cart
            foreach ($_SESSION['buynow_cart'] as $productId => $quantity) {
                // Determine if we should overwrite or add. User said "add... to cart".
                // Since main cart persists, using add() which increments is safer/correct.
                $this->cartService->add($productId, $quantity);
            }
            unset($_SESSION['buynow_cart']);
        }

        // Otherwise just redirect to cart
        header('Location: cart.php');
        exit;
    }
}
