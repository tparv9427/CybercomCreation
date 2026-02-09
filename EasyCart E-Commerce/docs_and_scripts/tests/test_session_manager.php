<?php
// tests/test_session_manager.php

echo "--- Testing Session Manager ---\n";

// Mock session if needed, but we can just let initSession handle it.
// We need to run this in CLI. Session in CLI behaves differently, but we can simulate $_SESSION array.

// 1. Setup Environment
// We need to require config, but config calls initSession which calls session_start.
// In CLI, session_start usually works but creates a file.
// We want to test logic.

require_once __DIR__ . '/../includes/session-manager.php';

// Reset everything
$_SESSION = [];
// Clear data files for test
file_put_contents(__DIR__ . '/../data/user_carts.json', '{}');
file_put_contents(__DIR__ . '/../data/user_wishlists.json', '{}');

echo "[1] Guest Session Test\n";
// Add to guest cart
$cart = getCart();
$cart[101] = 2; // Add 2 of product 101
setCart($cart);

$checkCart = getCart();
if (isset($checkCart[101]) && $checkCart[101] === 2) {
    echo "PASS: Guest cart item added.\n";
} else {
    echo "FAIL: Guest cart item not found.\n";
}

echo "\n[2] Login & Merge Test\n";
// Simulate Login
$userId = 999;
$_SESSION['user_id'] = $userId;
// Use merge logic directly
mergeGuestToUser($userId);

$userCart = getCart();
if (isset($userCart[101]) && $userCart[101] === 2) {
    echo "PASS: Cart merged to user.\n";
} else {
    echo "FAIL: Cart not merged.\n";
    print_r($userCart);
}

// Check File Persistence
$fileData = json_decode(file_get_contents(__DIR__ . '/../data/user_carts.json'), true);
if (isset($fileData[$userId][101]) && $fileData[$userId][101] === 2) {
    echo "PASS: User cart saved to disk.\n";
} else {
    echo "FAIL: User cart not on disk.\n";
    print_r($fileData);
}

echo "\n[3] Logout Test\n";
// Simulate Logout (Clear Session)
$_SESSION = [];
// Verify cleanly logged out (Guest state)
$guestCart = getCart();
if (empty($guestCart)) {
    echo "PASS: Guest cart empty after logout.\n";
} else {
    echo "FAIL: Guest cart not empty ?? It should be a new session theoretically.\n";
    // In CLI $_SESSION persists unless unset. We did $_SESSION = [].
}

echo "\n[4] Re-Login Persistence Test\n";
// Login again
$_SESSION['user_id'] = $userId;
loadUserDataToSession($userId);

$reloadedCart = getCart();
if (isset($reloadedCart[101]) && $reloadedCart[101] === 2) {
    echo "PASS: User cart restored from disk.\n";
} else {
    echo "FAIL: User cart not restored.\n";
}

echo "\n--- Test Complete ---\n";
?>
