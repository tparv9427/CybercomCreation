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
            $cart = getCart();
            if (!isset($cart[$product_id])) {
                $cart[$product_id] = 0;
            }
            $cart[$product_id] += $quantity;
            setCart($cart);
            
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
            $cart = getCart();
            $cart[$product_id] = $quantity;
            setCart($cart);
            
            // Calculate new values
            $product = getProduct($product_id);
            $item_total = $product['price'] * $quantity;
            
            $subtotal = 0;
            $total_items = 0;
            foreach ($cart as $pid => $qty) {
                $p = getProduct($pid);
                if ($p) {
                    $subtotal += $p['price'] * $qty;
                    $total_items += $qty;
                }
            }
            
            // Shipping: $10 per product in cart
            $shipping = $total_items * 10;
            // Tax: 8% on subtotal only
            $tax = $subtotal * 0.08;
            $total = $subtotal + $shipping + $tax;
            $free_shipping_remaining = 0;
            
            $response = [
                'success' => true,
                'cart_count' => getCartCount(),
                'item_total' => formatPrice($item_total),
                'subtotal' => formatPrice($subtotal),
                'shipping' => formatPrice($shipping),
                'tax' => formatPrice($tax),
                'total' => formatPrice($total),
                'free_shipping_remaining' => $free_shipping_remaining > 0 ? formatPrice($free_shipping_remaining) : null
            ];
        }
        break;
    
    case 'remove':
        $cart = getCart();
        if (isset($cart[$product_id])) {
            unset($cart[$product_id]);
            setCart($cart);
            
            // Recalculate totals
            $subtotal = 0;
            $total_items = 0;
            foreach ($cart as $pid => $qty) {
                $p = getProduct($pid);
                if ($p) {
                    $subtotal += $p['price'] * $qty;
                    $total_items += $qty;
                }
            }
            
            // Shipping: $10 per product in cart
            $shipping = $total_items * 10;
            // Tax: 8% on subtotal only
            $tax = $subtotal * 0.08;
            $total = $subtotal + $shipping + $tax;
            $free_shipping_remaining = 0;
            
            $response = [
                'success' => true,
                'cart_count' => getCartCount(),
                'message' => 'Product removed from cart',
                'subtotal' => formatPrice($subtotal),
                'shipping' => formatPrice($shipping),
                'tax' => formatPrice($tax),
                'total' => formatPrice($total),
                'free_shipping_remaining' => $free_shipping_remaining > 0 ? formatPrice($free_shipping_remaining) : null
            ];
        }
        break;
}

echo json_encode($response);
?>
