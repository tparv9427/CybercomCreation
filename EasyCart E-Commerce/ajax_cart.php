<?php
require_once 'includes/config.php';
header('Content-Type: application/json');

$action = isset($_POST['action']) ? $_POST['action'] : '';
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

$response = ['success' => false];

switch ($action) {
    case 'add':
        if ($product_id && getProduct($product_id)) {
            $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
            if (!isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id] = 0;
            }
            $_SESSION['cart'][$product_id] += $quantity;
            $response = [
                'success' => true,
                'cart_count' => getCartCount(),
                'message' => 'Product added to cart'
            ];
        }
        break;
    
    case 'update':
        $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
        if ($product_id && $quantity > 0) {
            $_SESSION['cart'][$product_id] = $quantity;
            $response = [
                'success' => true,
                'cart_count' => getCartCount(),
                'cart_total' => getCartTotal()
            ];
        }
        break;
    
    case 'remove':
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
            $response = [
                'success' => true,
                'cart_count' => getCartCount(),
                'message' => 'Product removed from cart'
            ];
        }
        break;
}

echo json_encode($response);
