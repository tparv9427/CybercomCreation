<?php

namespace EasyCart\Controller;

use EasyCart\Services\AuthService;
use EasyCart\Services\CartService;
use EasyCart\Services\PricingService;
use EasyCart\Services\CouponService;
use EasyCart\Collection\Collection_Product;
use EasyCart\Collection\Collection_Category;
use EasyCart\Resource\Resource_Order;
use EasyCart\View\View_Checkout;
use EasyCart\Helpers\FormatHelper;

/**
 * Controller_Checkout — Checkout Flow
 * 
 * No SQL, no HTML. Uses Collection + View classes.
 */
class Controller_Checkout extends Controller_Abstract
{
    private $authService;
    private $cartService;
    private $pricingService;
    private $productCollection;
    private $categoryCollection;
    private $orderResource;
    private $couponService;

    public function __construct()
    {
        $this->authService = new AuthService();
        $this->cartService = new CartService();
        $this->pricingService = new PricingService();
        $this->productCollection = new Collection_Product();
        $this->categoryCollection = new Collection_Category();
        $this->orderResource = new Resource_Order();
        $this->couponService = new CouponService();
    }

    /**
     * Checkout page
     */
    public function index(): void
    {
        if (!AuthService::check()) {
            $_SESSION['checkout_redirect'] = true;
            if (isset($_GET['action']) && $_GET['action'] === 'buynow') {
                $_SESSION['pending_buynow'] = ['id' => $_GET['id'], 'quantity' => $_GET['quantity']];
            }
            $this->redirect('/login');
        }

        if (isset($_SESSION['pending_buynow'])) {
            $pending = $_SESSION['pending_buynow'];
            unset($_SESSION['pending_buynow']);
            $this->redirect("/checkout?action=buynow&id={$pending['id']}&quantity={$pending['quantity']}");
        }

        if (isset($_GET['action']) && $_GET['action'] === 'cancel') {
            $this->cancel();
        }

        $categories = $this->categoryCollection->getAll();
        $isBuyNow = isset($_GET['action']) && $_GET['action'] === 'buynow';

        if ($isBuyNow && isset($_GET['id'])) {
            $productId = (int) $_GET['id'];
            $quantity = isset($_GET['quantity']) ? (int) $_GET['quantity'] : 1;
            $product = $this->productCollection->findById($productId);
            if ($product) {
                $_SESSION['buynow_cart'] = [$productId => $quantity];
                $cart = [$productId => $quantity];
            } else {
                $this->redirect('/');
            }
        } else {
            if (isset($_SESSION['buynow_cart']))
                unset($_SESSION['buynow_cart']);
            $cart = $this->cartService->get();
        }

        if (empty($cart) && !$isBuyNow) {
            $this->redirect('/');
        }

        $cart_items = [];
        foreach ($cart as $product_id => $quantity) {
            $product = $this->productCollection->findById($product_id);
            if ($product) {
                $cart_items[] = [
                    'product' => $product,
                    'quantity' => $quantity,
                    'total' => $product['price'] * $quantity
                ];
            }
        }

        $shipping_category = $this->pricingService->determineShippingCategory($cart);
        $allowed_methods = $this->pricingService->getAllowedShippingMethods($shipping_category);
        $shipping_method = $_SESSION['shipping_method'] ?? 'standard';
        if (!in_array($shipping_method, $allowed_methods)) {
            $shipping_method = $allowed_methods[0];
            $_SESSION['shipping_method'] = $shipping_method;
        }

        $payment_method = $_SESSION['payment_method'] ?? 'card';
        $coupon = $_SESSION['applied_coupon'] ?? null;
        $pricing = $this->pricingService->calculateAll($cart, $shipping_method, $payment_method, $coupon);

        $formatPrice = function ($price) {
            return FormatHelper::price($price);
        };

        $contentView = new View_Checkout([
            'cart_items' => $cart_items,
            'pricing' => $pricing,
            'subtotal' => $pricing['subtotal'],
            'shipping' => $pricing['shipping'],
            'payment_fee' => $pricing['payment_fee'],
            'tax' => $pricing['tax'],
            'total' => $pricing['total'],
            'total_items' => $pricing['item_count'],
            'shipping_category' => $shipping_category,
            'allowed_category' => $shipping_category,
            'allowed_methods' => $allowed_methods,
            'shipping_method' => $shipping_method,
            'payment_method' => $payment_method,
            'coupon' => $coupon,
            'isBuyNow' => $isBuyNow,
            'formatPrice' => $formatPrice,
            'categories' => $categories,
        ]);

        $this->renderWithLayout($contentView, [
            'page_title' => 'Checkout',
            'categories' => $categories,
        ]);
    }

    /**
     * Get updated pricing (AJAX)
     */
    public function pricing(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            $this->jsonResponse(['error' => 'Method not allowed']);
            return;
        }

        $shippingMethod = $_POST['shipping'] ?? 'standard';
        $paymentMethod = $_POST['payment'] ?? 'card';
        $_SESSION['shipping_method'] = $shippingMethod;
        $_SESSION['payment_method'] = $paymentMethod;

        $cart = isset($_SESSION['buynow_cart']) ? $_SESSION['buynow_cart'] : $this->cartService->get();
        $pricing = $this->pricingService->calculateAll($cart, $shippingMethod, $paymentMethod);

        $this->jsonResponse([
            'success' => true,
            'pricing' => [
                'subtotal' => FormatHelper::price($pricing['subtotal']),
                'shipping' => FormatHelper::price($pricing['shipping']),
                'payment_fee' => $pricing['payment_fee'] > 0 ? FormatHelper::price($pricing['payment_fee']) : null,
                'tax' => FormatHelper::price($pricing['tax']),
                'total' => FormatHelper::price($pricing['total'])
            ]
        ]);
    }

    /**
     * Process order (POST)
     */
    public function process(): void
    {
        if (!AuthService::check()) {
            $this->redirect('/login');
        }

        $user = $this->authService->getCurrentUser();
        $userId = $user['id'] ?? $user['entity_id'];

        $isBuyNowOrder = isset($_SESSION['buynow_cart']);
        $cart = $isBuyNowOrder ? $_SESSION['buynow_cart'] : $this->cartService->get();

        if (empty($cart)) {
            $this->redirect('/');
        }

        $shipping_category = $this->pricingService->determineShippingCategory($cart);
        $allowed_methods = $this->pricingService->getAllowedShippingMethods($shipping_category);
        $shipping_method = $_SESSION['shipping_method'] ?? 'standard';
        if (!in_array($shipping_method, $allowed_methods)) {
            $shipping_method = $allowed_methods[0];
            $_SESSION['shipping_method'] = $shipping_method;
        }

        $coupon = $_SESSION['applied_coupon'] ?? null;
        $pricing = $this->pricingService->calculateAll($cart, $shipping_method, 'card', $coupon);

        $orderItems = [];
        foreach ($cart as $product_id => $quantity) {
            $product = $this->productCollection->findById($product_id);
            if ($product) {
                $orderItems[] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'price' => $product['price'],
                    'sku' => $product['sku'] ?? '',
                    'quantity' => $quantity,
                ];
            }
        }

        $order_number = 'ORD-' . strtoupper(substr(md5(time() . rand()), 0, 8));

        $orderData = [
            'order_number' => $order_number,
            'user_id' => $userId,
            'subtotal' => $pricing['subtotal'],
            'shipping_cost' => $pricing['shipping'],
            'tax' => $pricing['tax'],
            'discount' => $pricing['discount'] ?? 0,
            'total' => $pricing['total'],
            'status' => 'Processing',
            'original_cart_id' => $isBuyNowOrder ? null : $this->cartService->getCartId()
        ];

        $dbOrderId = $this->orderResource->createOrder($orderData, $orderItems);

        if (!$dbOrderId) {
            die("Order processing failed.");
        }

        $this->orderResource->addAddress($dbOrderId, 'billing', [
            'name' => $_POST['billing_name'] ?? '',
            'email' => $user['email'] ?? '',
            'phone' => $_POST['billing_phone'] ?? '',
            'address' => $_POST['billing_address'] ?? '',
            'city' => $_POST['billing_city'] ?? '',
            'zip' => $_POST['billing_zip'] ?? ''
        ]);

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
            'email' => $user['email'] ?? '',
            'phone' => $_POST['shipping_phone'] ?? '',
            'address' => $_POST['shipping_address'] ?? '',
            'city' => $_POST['shipping_city'] ?? '',
            'zip' => $_POST['shipping_zip'] ?? ''
        ];

        $this->orderResource->addAddress($dbOrderId, 'shipping', $shippingData);

        $payment_method = $_SESSION['payment_method'] ?? 'card';
        $this->orderResource->addPaymentInfo($dbOrderId, $shipping_method, $payment_method);

        $_SESSION['last_order_id'] = $order_number;

        if (!$isBuyNowOrder) {
            $this->cartService->empty();
        } else {
            unset($_SESSION['buynow_cart']);
        }

        $this->redirect('/order/success');
    }

    /**
     * Cancel checkout
     */
    private function cancel(): void
    {
        if (isset($_SESSION['buynow_cart'])) {
            foreach ($_SESSION['buynow_cart'] as $productId => $quantity) {
                $this->cartService->add($productId, $quantity);
            }
            unset($_SESSION['buynow_cart']);
        }
        $this->redirect('/cart');
    }

    /**
     * Apply/Remove Coupon (AJAX)
     */
    public function coupon(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            $this->jsonResponse(['success' => false, 'message' => 'Method not allowed']);
            return;
        }

        $action = $_POST['action'] ?? 'apply';

        if ($action === 'remove') {
            unset($_SESSION['applied_coupon']);
            $cart = $this->cartService->get();
            $shippingMethod = $_SESSION['shipping_method'] ?? 'standard';
            $paymentMethod = $_SESSION['payment_method'] ?? 'card';
            $pricing = $this->pricingService->calculateAll($cart, $shippingMethod, $paymentMethod);

            $this->jsonResponse([
                'success' => true,
                'message' => 'Coupon removed',
                'new_total' => FormatHelper::price($pricing['total'])
            ]);
            return;
        }

        $code = $_POST['code'] ?? '';
        if (empty($code)) {
            $this->jsonResponse(['success' => false, 'message' => 'Please enter a coupon code']);
            return;
        }

        $coupon = $this->couponService->validateCoupon($code);
        if ($coupon) {
            $_SESSION['applied_coupon'] = $coupon;
            $cart = $this->cartService->get();
            $shippingMethod = $_SESSION['shipping_method'] ?? 'standard';
            $paymentMethod = $_SESSION['payment_method'] ?? 'card';
            $pricing = $this->pricingService->calculateAll($cart, $shippingMethod, $paymentMethod, $coupon);

            $this->jsonResponse([
                'success' => true,
                'message' => 'Coupon applied successfully!',
                'discount_percent' => $coupon['percent'],
                'discount_amount' => FormatHelper::price($pricing['discount']),
                'new_total' => FormatHelper::price($pricing['total'])
            ]);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Invalid coupon code']);
        }
    }
}
