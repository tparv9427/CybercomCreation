<?php
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Repositories/CartRepository.php';
require_once __DIR__ . '/../app/Repositories/WishlistRepository.php';
require_once __DIR__ . '/../app/Services/CouponService.php';

use EasyCart\Repositories\CartRepository;
use EasyCart\Repositories\WishlistRepository;
use EasyCart\Services\CouponService;

session_start();
$_SESSION['user_id'] = 19; // Test user from previous cart migration

echo "Testing CartRepository...\n";
$cartRepo = new CartRepository();
$cart = $cartRepo->loadFromDisk(19); // Should load from DB now
echo "Cart items count: " . count($cart) . "\n";

echo "Testing WishlistRepository...\n";
$wishRepo = new WishlistRepository();
$wishlist = $wishRepo->get(19);
echo "Wishlist items count: " . count($wishlist) . "\n";

echo "Testing CouponService...\n";
$couponService = new CouponService();
$coupon = $couponService->validateCoupon('SAVE10'); // Assuming SAVE10 exists
if ($coupon) {
    echo "Coupon SAVE10 found: " . $coupon['percent'] . "%\n";
} else {
    echo "Coupon SAVE10 not found (expected if not in data)\n";
}

echo "Verification Complete.\n";
