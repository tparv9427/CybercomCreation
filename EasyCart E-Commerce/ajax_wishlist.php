<?php
require_once 'includes/config.php';

// Simulate server delay (v4.0-fake-server)
simulateDelay('AJAX_DELAY');

header('Content-Type: application/json');

$action = isset($_POST['action']) ? $_POST['action'] : '';
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

$response = ['success' => false];

if ($action === 'toggle' && $product_id && getProduct($product_id)) {
    $key = array_search($product_id, $_SESSION['wishlist']);
    if ($key !== false) {
        unset($_SESSION['wishlist'][$key]);
        $_SESSION['wishlist'] = array_values($_SESSION['wishlist']);
        $in_wishlist = false;
    } else {
        $_SESSION['wishlist'][] = $product_id;
        $in_wishlist = true;
    }
    
    $response = [
        'success' => true,
        'in_wishlist' => $in_wishlist,
        'wishlist_count' => getWishlistCount()
    ];
}

echo json_encode($response);
