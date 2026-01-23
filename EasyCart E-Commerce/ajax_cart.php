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
            
            // Calculate new values
            $product = getProduct($product_id);
            $item_total = $product['price'] * $quantity;
            
            $subtotal = 0;
            foreach ($_SESSION['cart'] as $pid => $qty) {
                $p = getProduct($pid);
                if ($p) {
                    $subtotal += $p['price'] * $qty;
                }
            }
            
            $shipping = $subtotal > 50 ? 0 : 10;
            $tax = $subtotal * 0.08;
            $total = $subtotal + $shipping + $tax;
            $free_shipping_remaining = $subtotal < 50 ? (50 - $subtotal) : 0;
            
            $response = [
                'success' => true,
                'cart_count' => getCartCount(),
                'item_total' => formatPrice($item_total),
                'subtotal' => formatPrice($subtotal),
                'shipping' => $shipping > 0 ? formatPrice($shipping) : 'FREE',
                'tax' => formatPrice($tax),
                'total' => formatPrice($total),
                'free_shipping_remaining' => $free_shipping_remaining > 0 ? formatPrice($free_shipping_remaining) : null
            ];
        }
        break;
    
    case 'remove':
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
            
            // Recalculate totals
            $subtotal = 0;
            foreach ($_SESSION['cart'] as $pid => $qty) {
                $p = getProduct($pid);
                if ($p) {
                    $subtotal += $p['price'] * $qty;
                }
            }
            
            $shipping = $subtotal > 50 ? 0 : 10;
            $tax = $subtotal * 0.08;
            $total = $subtotal + $shipping + $tax;
            $free_shipping_remaining = $subtotal < 50 ? (50 - $subtotal) : 0;
            
            $response = [
                'success' => true,
                'cart_count' => getCartCount(),
                'message' => 'Product removed from cart',
                'subtotal' => formatPrice($subtotal),
                'shipping' => $shipping > 0 ? formatPrice($shipping) : 'FREE',
                'tax' => formatPrice($tax),
                'total' => formatPrice($total),
                'free_shipping_remaining' => $free_shipping_remaining > 0 ? formatPrice($free_shipping_remaining) : null
            ];
        }
        break;
}

echo json_encode($response);
?>
