<?php
require_once 'includes/config.php';

// Redirect to login if not logged in
if (!isLoggedIn()) {
    $_SESSION['checkout_redirect'] = true;
    header('Location: login.php');
    exit;
}

$page_title = 'Checkout';
$cart_items = [];
$subtotal = 0;

foreach ($_SESSION['cart'] as $product_id => $quantity) {
    $product = getProduct($product_id);
    if ($product) {
        $cart_items[] = [
            'product' => $product,
            'quantity' => $quantity,
            'total' => $product['price'] * $quantity
        ];
        $subtotal += $product['price'] * $quantity;
    }
}

$shipping = $subtotal > 50 ? 0 : 10;
$tax = $subtotal * 0.08;
$total = $subtotal + $shipping + $tax;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process order
    $order_id = 'ORD-' . strtoupper(substr(md5(time()), 0, 8));
    $_SESSION['last_order_id'] = $order_id;
    $_SESSION['cart'] = [];
    header('Location: order-success.php');
    exit;
}

include 'includes/header.php';
?>

<link rel="stylesheet" href="assets/css/checkout.css">

<div class="breadcrumb">
    <a href="index.php">Home</a> / <a href="cart.php">Cart</a> / Checkout
</div>

<div class="container">
    <div class="section-header">
        <h2 class="section-title">Checkout</h2>
        <p class="section-subtitle">Complete your purchase</p>
    </div>

    <div class="checkout-layout">
        <div class="checkout-form">
            <form method="POST">
                <div class="form-section">
                    <h3>Shipping Information</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Full Name *</label>
                            <input type="text" name="name" required value="<?php echo $_SESSION['user_name'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Phone Number *</label>
                            <input type="tel" name="phone" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Address *</label>
                        <input type="text" name="address" required>
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>City *</label>
                            <input type="text" name="city" required>
                        </div>
                        <div class="form-group">
                            <label>Zip Code *</label>
                            <input type="text" name="zip" required>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Shipping Method</h3>
                    <div class="shipping-options">
                        <label class="shipping-option">
                            <input type="radio" name="shipping" value="standard" checked>
                            <div class="option-content">
                                <strong>Standard Shipping (3-5 days)</strong>
                                <span><?php echo $shipping > 0 ? formatPrice($shipping) : 'FREE'; ?></span>
                            </div>
                        </label>
                        <label class="shipping-option">
                            <input type="radio" name="shipping" value="express">
                            <div class="option-content">
                                <strong>Express Shipping (1-2 days)</strong>
                                <span>$25.00</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Payment Method</h3>
                    <div class="payment-options">
                        <label class="payment-option">
                            <input type="radio" name="payment" value="card" checked>
                            <span>💳 Credit/Debit Card</span>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment" value="upi">
                            <span>📱 UPI</span>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment" value="netbanking">
                            <span>🏦 Net Banking</span>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment" value="wallet">
                            <span>👛 Wallet</span>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment" value="cod">
                            <span>💵 Cash on Delivery</span>
                        </label>
                    </div>
                    
                    <div class="card-details" id="cardDetails">
                        <div class="form-group">
                            <label>Card Number</label>
                            <input type="text" placeholder="1234 5678 9012 3456" maxlength="19">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Expiry Date</label>
                                <input type="text" placeholder="MM/YY" maxlength="5">
                            </div>
                            <div class="form-group">
                                <label>CVV</label>
                                <input type="text" placeholder="123" maxlength="3">
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-place-order">Place Order</button>
            </form>
        </div>

        <div class="order-summary-sidebar">
            <h3>Order Summary</h3>
            <div class="summary-items">
                <?php foreach ($cart_items as $item): ?>
                    <div class="summary-item">
                        <div class="summary-item-info">
                            <span><?php echo $item['product']['name']; ?></span>
                            <span class="item-qty">× <?php echo $item['quantity']; ?></span>
                        </div>
                        <span class="summary-item-price"><?php echo formatPrice($item['total']); ?></span>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="summary-totals">
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span><?php echo formatPrice($subtotal); ?></span>
                </div>
                <div class="summary-row">
                    <span>Shipping:</span>
                    <span><?php echo $shipping > 0 ? formatPrice($shipping) : 'FREE'; ?></span>
                </div>
                <div class="summary-row">
                    <span>Tax (8%):</span>
                    <span><?php echo formatPrice($tax); ?></span>
                </div>
                <div class="summary-total">
                    <span>Total:</span>
                    <span><?php echo formatPrice($total); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
