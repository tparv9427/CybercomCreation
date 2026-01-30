<?php
/**
 * AJAX Endpoint for Checkout Pricing Updates
 * 
 * Handles dynamic pricing calculations when shipping method changes
 * Returns JSON with updated shipping, tax, and total amounts
 */

// Start session
session_start();

// Load configuration and autoloader
require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/autoload.php';

use EasyCart\Helpers\PricingHelper;

// Set JSON header
header('Content-Type: application/json');

try {
    // Verify this is a POST request
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }

    // Get shipping method from POST data
    $shippingMethod = $_POST['shipping'] ?? 'standard';

    // Get payment method from POST data
    $paymentMethod = $_POST['payment'] ?? 'card';

    // Validate shipping method
    $validMethods = ['standard', 'express', 'white_glove', 'freight'];
    if (!in_array($shippingMethod, $validMethods)) {
        $shippingMethod = 'standard';
    }

    // Save to session
    $_SESSION['shipping_method'] = $shippingMethod;
    $_SESSION['payment_method'] = $paymentMethod;

    // Check for buy now cart
    if (isset($_SESSION['buynow_cart'])) {
        $cart = $_SESSION['buynow_cart'];
    } else {
        $cart = $_SESSION['cart'] ?? [];
    }

    // Check if cart is empty
    if (empty($cart)) {
        echo json_encode([
            'success' => false,
            'message' => 'Cart is empty'
        ]);
        exit;
    }

    // Use ProductRepository to get product data
    $productRepo = new \EasyCart\Repositories\ProductRepository();

    // Build cart items array with prices
    $cartItems = [];
    foreach ($cart as $productId => $quantity) {
        $product = $productRepo->find($productId);

        if ($product) {
            $cartItems[] = [
                'id' => $productId,
                'quantity' => $quantity,
                'price' => $product['price']
            ];
        }
    }

    // Calculate pricing using PricingHelper
    $pricing = PricingHelper::calculateCheckoutPricing($cartItems, $shippingMethod, $paymentMethod);

    // Return success response
    echo json_encode([
        'success' => true,
        'pricing' => [
            'subtotal' => $pricing['subtotal_formatted'],
            'shipping' => $pricing['shipping_formatted'],
            'payment_fee' => $pricing['payment_fee'] > 0 ? $pricing['payment_fee_formatted'] : null,
            'tax' => $pricing['tax_formatted'],
            'total' => $pricing['total_formatted']
        ],
        'raw' => [
            'subtotal' => $pricing['subtotal'],
            'shipping' => $pricing['shipping'],
            'payment_fee' => $pricing['payment_fee'],
            'tax' => $pricing['tax'],
            'total' => $pricing['total']
        ]
    ]);

} catch (Exception $e) {
    // Return error response
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
