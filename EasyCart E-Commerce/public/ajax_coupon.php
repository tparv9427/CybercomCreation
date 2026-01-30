<?php
require_once __DIR__ . '/../vendor/autoload.php';

use EasyCart\Services\CouponService;
use EasyCart\Helpers\FormatHelper;

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$action = $_POST['action'] ?? 'apply';

if ($action === 'remove') {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (isset($_SESSION['applied_coupon'])) {
        unset($_SESSION['applied_coupon']);
    }

    // Recalculate total without discount
    $cartService = new \EasyCart\Services\CartService();
    $pricingService = new \EasyCart\Services\PricingService();

    $cart = $cartService->get();
    $pricing = $pricingService->calculateAll($cart, 'standard');

    echo json_encode([
        'success' => true,
        'message' => 'Coupon removed',
        'new_total' => FormatHelper::price($pricing['total'])
    ]);
    exit;
}

$code = $_POST['code'] ?? '';
$subtotal = isset($_POST['subtotal']) ? (float) preg_replace('/[^0-9.]/', '', $_POST['subtotal']) : 0;
// Note: In a real app, subtotal should be recalculated on server to prevent tampering. 
// For this task scope, we assume the passed subtotal is reliable or will be re-verified if needed. 
// Ideally, we should fetch the cart again. Let's do a quick cart fetch to be safe if possible, 
// or stick to the simple requirement. The request implied checking validity.
// Let's recalculate subtotal from session/cart because trusting client subtotal is bad practice.

// Re-initialize app components to get cart total safely
session_start();
$cartController = new \EasyCart\Controllers\CartController();
// We can't easily access CartController internal logic without re-instantiating everything.
// For potential conflicts, let's stick to using the Service directly if possible or trust the prompt's context.
// The prompt said "match them with theme... discount on subtotal".
// Let's rely on CartService + PricingService for the "correct" subtotal.

$cartService = new \EasyCart\Services\CartService();
$pricingService = new \EasyCart\Services\PricingService();
$productRepo = new \EasyCart\Repositories\ProductRepository();

$cart = $cartService->get();
$pricing = $pricingService->calculateAll($cart, 'standard'); // Default shipping for now, just need subtotal
$realSubtotal = $pricing['subtotal'];

$couponService = new CouponService();
$result = $couponService->validateCoupon($code);

if ($result) {
    $discountPercent = $result['percent'];
    $discountAmount = ($realSubtotal * $discountPercent) / 100;

    // Recalculate Total
    // We need to apply this discount.
    // NOTE: The current PricingService doesn't know about coupons.
    // We will calculate the new total here for the UI response.
    // In a full implementation, we'd store the coupon in the session so it persists.

    // Store coupon in session
    $_SESSION['applied_coupon'] = [
        'code' => $result['code'],
        'percent' => $result['percent'],
        'amount' => $discountAmount
    ];

    $newTotal = $pricing['total'] - $discountAmount;

    echo json_encode([
        'success' => true,
        'message' => 'Coupon applied successfully!',
        'discount_percent' => $discountPercent,
        'discount_amount' => FormatHelper::price($discountAmount),
        'new_total' => FormatHelper::price($newTotal)
    ]);
} else {
    // Clear coupon from session if invalid attempt? No, user might just mistyped.
    // If they explicitly remove it, we'd handle that. Here just return error.

    echo json_encode([
        'success' => false,
        'message' => 'Invalid coupon code'
    ]);
}
