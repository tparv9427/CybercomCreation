<?php
require_once 'includes/config.php';
$page_title = 'Shopping Cart';

$cart_items = [];
$subtotal = 0;
$total_items = 0;

$cart = getCart();
foreach ($cart as $product_id => $quantity) {
    $product = getProduct($product_id);
    if ($product) {
        $cart_items[] = [
            'product' => $product,
            'quantity' => $quantity,
            'total' => $product['price'] * $quantity
        ];
        $subtotal += $product['price'] * $quantity;
        $total_items += $quantity;
    }
}

// Shipping: $10 per product in cart
$shipping = $total_items * 10;
// Tax: 8% on subtotal only
$tax = $subtotal * 0.08;
$total = $subtotal + $shipping + $tax;

include 'includes/header.php';
?>

<link rel="stylesheet" href="assets/css/cart.css">

<div class="breadcrumb">
    <a href="index.php">Home</a> / Shopping Cart
</div>

<div class="container">
    <div class="section-header">
        <h2 class="section-title">Shopping Cart</h2>
        <p class="section-subtitle"><?php echo count($cart_items); ?> item(s) in your cart</p>
    </div>

    <?php if (count($cart_items) > 0): ?>
        <div class="cart-layout">
            <div class="cart-items">
                <?php foreach ($cart_items as $item): ?>
                    <div class="cart-item" data-product-id="<?php echo $item['product']['id']; ?>">
                        <div class="item-image" onclick="window.location.href='product.php?id=<?php echo $item['product']['id']; ?>'"><?php echo $item['product']['icon']; ?></div>
                        <div class="item-details">
                            <h3 class="item-name"><?php echo $item['product']['name']; ?></h3>
                            <p class="item-category"><?php echo getCategory($item['product']['category_id'])['name']; ?></p>
                            <p class="item-price"><?php echo formatPrice($item['product']['price']); ?></p>
                        </div>
                        <div class="item-quantity">
                            <div class="quantity-controls">
                                <button onclick="decreaseCartQuantity(<?php echo $item['product']['id']; ?>)">−</button>
                                <input type="number" class="quantity-input" id="qty-<?php echo $item['product']['id']; ?>" value="<?php echo $item['quantity']; ?>" min="1" readonly>
                                <button onclick="increaseCartQuantity(<?php echo $item['product']['id']; ?>)">+</button>
                            </div>
                        </div>
                        <div class="item-total"><?php echo formatPrice($item['total']); ?></div>
                        <button class="item-remove" onclick="removeFromCart(<?php echo $item['product']['id']; ?>)">×</button>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary">
                <h3>Order Summary</h3>
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span><?php echo formatPrice($subtotal); ?></span>
                </div>
                <div class="summary-row">
                    <span>Shipping:</span>
                    <span><?php echo formatPrice($shipping); ?></span>
                </div>
                <?php if ($total_items > 0): ?>
                    <div class="free-shipping-notice">
                        Shipping: $10.00 per item (<?php echo $total_items; ?> item<?php echo $total_items != 1 ? 's' : ''; ?>)
                    </div>
                <?php endif; ?>
                <div class="summary-row">
                    <span>Tax (8%):</span>
                    <span><?php echo formatPrice($tax); ?></span>
                </div>
                <div class="summary-total">
                    <span>Total:</span>
                    <span><?php echo formatPrice($total); ?></span>
                </div>
                <a href="checkout.php" class="btn btn-primary">Proceed to Checkout</a>
                <a href="products.php" class="btn btn-outline">Continue Shopping</a>
            </div>
        </div>
    <?php else: ?>
        <div class="empty-cart">
            <div class="empty-icon">🛒</div>
            <h3>Your cart is empty</h3>
            <p>Add some products to get started!</p>
            <a href="products.php" class="btn">Shop Now</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
