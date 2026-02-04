<?php

namespace EasyCart\Controllers;

use EasyCart\Services\AuthService;
use EasyCart\Services\CartService;
use EasyCart\Services\PricingService;
use EasyCart\Repositories\ProductRepository;
use EasyCart\Repositories\OrderRepository;
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
    private $orderRepo;
    private $couponService;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->cartService = new CartService();
        $this->pricingService = new PricingService();
        $this->productRepo = new ProductRepository();
        $this->categoryRepo = new CategoryRepository();
        $this->orderRepo = new OrderRepository();
        $this->couponService = new \EasyCart\Services\CouponService();
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
            header('Location: /login');
            exit;
        }

        // Restore pending buy now if returning from login
        if (isset($_SESSION['pending_buynow'])) {
            $pending = $_SESSION['pending_buynow'];
            unset($_SESSION['pending_buynow']);
            // Redirect to self with params to properly set state
            header("Location: /checkout?action=buynow&id={$pending['id']}&quantity={$pending['quantity']}");
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
                header('Location: /'); // Invalid product
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
            header('Location: /');
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
        $coupon = $_SESSION['applied_coupon'] ?? null;

        $pricing = $this->pricingService->calculateAll($cart, $shipping_method, $payment_method, $coupon);
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
            header('Location: /login');
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
            header('Location: /'); // Redirect if cart is empty
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

        $coupon = $_SESSION['applied_coupon'] ?? null;
        $pricing = $this->pricingService->calculateAll($cart, $shipping_method, 'card', $coupon); // Defaulting payment to card or should be from session? index uses session.
        // The process method didn't get payment method from POST/Session in original. Assuming card/session.
        // Actually original process() call: $pricing = $this->pricingService->calculateAll($cart, $shipping_method);
        // And calculateAll default was 'card'. So preserving that behavior.

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
        // Generate Order Number
        $order_number = 'ORD-' . strtoupper(substr(md5(time() . rand()), 0, 8));

        $orderData = [
            'order_number' => $order_number,
            'user_id' => $userId,
            'subtotal' => $pricing['subtotal'],
            'shipping_cost' => $pricing['shipping'],
            'tax' => $pricing['tax'],
            'discount' => $pricing['discount'] ?? 0, // Add discount field
            'total' => $pricing['total'],
            'status' => 'Processing'
        ];

        // Save order to DB
        $dbOrderId = $this->orderRepo->save($orderData, $orderItems);

        if (!$dbOrderId) {
            // Handle error
            die("Order processing failed.");
        }

        // Save Billing Address
        $this->orderRepo->addAddress($dbOrderId, 'billing', [
            'name' => $_POST['billing_name'] ?? '',
            'email' => $user['email'] ?? '',
            'phone' => $_POST['billing_phone'] ?? '',
            'address' => $_POST['billing_address'] ?? '',
            'city' => $_POST['billing_city'] ?? '',
            'zip' => $_POST['billing_zip'] ?? ''
        ]);

        // Save Shipping Address
        // Check if "same as billing" is checked
        $sameAsBilling = isset($_POST['same_as_billing']);

        $shippingData = $sameAsBilling ? [
            'name' => $_POST['billing_name'] ?? '',
            'email' => $user['email'] ?? '',
            'phone' => $_POST['billing_phone'] ?? '',
            'address' => $_POST['billing_address'] ?? '',
            'city' => $_POST['billing_city'] ?? '',
            'zip' => $_POST['billing_zip'] ?? ''
        ] : [
            'name' => $_POST['shipping_name'] ?? '',
            'email' => $user['email'] ?? '', // Shipping email usually same as user?
            'phone' => $_POST['shipping_phone'] ?? '',
            'address' => $_POST['shipping_address'] ?? '',
            'city' => $_POST['shipping_city'] ?? '',
            'zip' => $_POST['shipping_zip'] ?? ''
        ];

        $this->orderRepo->addAddress($dbOrderId, 'shipping', $shippingData);

        // Save Payment and Shipping Method
        $payment_method = $_SESSION['payment_method'] ?? 'card';
        $this->orderRepo->addPaymentInfo($dbOrderId, $shipping_method, $payment_method);

        $_SESSION['last_order_id'] = $order_number; // Use number for display/success page lookup if needed

        // Clear cart ONLY if it's a regular order
        if (!$isBuyNowOrder) {
            $this->cartService->empty();
        } else {
            // If Buy Now, just clear the temp session
            unset($_SESSION['buynow_cart']);
        }

        header('Location: /order/success');
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
        header('Location: /cart');
        exit;
    }

    /**
     * Apply/Remove Coupon (AJAX)
     */
    public function coupon()
    {
        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
            exit;
        }

        $action = $_POST['action'] ?? 'apply';

        if ($action === 'remove') {
            unset($_SESSION['applied_coupon']);

            // Recalculate totals
            $cart = $this->cartService->get();
            $shippingMethod = $_SESSION['shipping_method'] ?? 'standard';
            $paymentMethod = $_SESSION['payment_method'] ?? 'card';
            $pricing = $this->pricingService->calculateAll($cart, $shippingMethod, $paymentMethod);

            echo json_encode([
                'success' => true,
                'message' => 'Coupon removed',
                'new_total' => \EasyCart\Helpers\FormatHelper::price($pricing['total'])
            ]);
            exit;
        }

        $code = $_POST['code'] ?? '';
        if (empty($code)) {
            echo json_encode(['success' => false, 'message' => 'Please enter a coupon code']);
            exit;
        }

        $coupon = $this->couponService->validateCoupon($code);

        if ($coupon) {
            $_SESSION['applied_coupon'] = $coupon;

            // Recalculate with discount
            $cart = $this->cartService->get();
            $shippingMethod = $_SESSION['shipping_method'] ?? 'standard';
            $paymentMethod = $_SESSION['payment_method'] ?? 'card';

            $pricing = $this->pricingService->calculateAll($cart, $shippingMethod, $paymentMethod, $coupon);

            echo json_encode([
                'success' => true,
                'message' => 'Coupon applied successfully!',
                'discount_percent' => $coupon['percent'],
                'discount_amount' => \EasyCart\Helpers\FormatHelper::price($pricing['discount']),
                'new_total' => \EasyCart\Helpers\FormatHelper::price($pricing['total'])
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid coupon code']);
        }
        exit;
    }
}
