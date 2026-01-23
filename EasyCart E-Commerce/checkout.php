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
            <form method="POST" id="checkoutForm">
                <div class="form-section">
                    <h3>Shipping Information</h3>
                    <div class="form-row">
                        <div class="form-group">
                            <label>Full Name *</label>
                            <input type="text" name="name" required value="<?php echo $_SESSION['user_name'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Phone Number *</label>
                            <input type="tel" name="phone" required placeholder="10-digit number" maxlength="10" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Address *</label>
                        <input type="text" name="address" required placeholder="Street address, building name">
                    </div>
                    <div class="form-row">
                        <div class="form-group">
                            <label>City *</label>
                            <input type="text" name="city" required>
                        </div>
                        <div class="form-group">
                            <label>Zip Code *</label>
                            <input type="text" name="zip" required placeholder="6-digit pincode" maxlength="6">
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Shipping Method</h3>
                    <div class="shipping-options">
                        <label class="shipping-option" data-shipping="standard">
                            <input type="radio" name="shipping" value="standard" checked>
                            <div class="option-content">
                                <strong>Standard Shipping (3-5 days)</strong>
                                <span><?php echo $shipping > 0 ? formatPrice($shipping) : 'FREE'; ?></span>
                            </div>
                        </label>
                        <label class="shipping-option" data-shipping="express">
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
                        <label class="payment-option" data-payment="card">
                            <input type="radio" name="payment" value="card" checked>
                            <span>💳 Credit/Debit Card</span>
                        </label>
                        <label class="payment-option" data-payment="upi">
                            <input type="radio" name="payment" value="upi">
                            <span>📱 UPI</span>
                        </label>
                        <label class="payment-option" data-payment="netbanking">
                            <input type="radio" name="payment" value="netbanking">
                            <span>🏦 Net Banking</span>
                        </label>
                        <label class="payment-option" data-payment="wallet">
                            <input type="radio" name="payment" value="wallet">
                            <span>👛 Digital Wallet</span>
                        </label>
                        <label class="payment-option" data-payment="cod">
                            <input type="radio" name="payment" value="cod">
                            <span>💵 Cash on Delivery</span>
                        </label>
                    </div>
                    
                    <!-- Card Details -->
                    <div class="payment-details card-details active" id="cardDetails">
                        <h4 style="margin-bottom: 1rem; color: var(--primary);">Enter Card Details</h4>
                        <div class="form-group">
                            <label>Card Number</label>
                            <input type="text" id="cardNumber" placeholder="1234 5678 9012 3456" maxlength="19">
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label>Expiry Date</label>
                                <input type="text" id="cardExpiry" placeholder="MM/YY" maxlength="5">
                            </div>
                            <div class="form-group">
                                <label>CVV</label>
                                <input type="text" id="cardCVV" placeholder="123" maxlength="3">
                            </div>
                        </div>
                    </div>

                    <!-- UPI Details -->
                    <div class="payment-details upi-details" id="upiDetails">
                        <h4 style="margin-bottom: 1rem; color: var(--primary);">Pay via UPI</h4>
                        <div class="form-group">
                            <label>Enter UPI ID</label>
                            <input type="text" placeholder="yourname@upi">
                        </div>
                        <p style="text-align: center; margin: 1rem 0; color: var(--secondary);">OR</p>
                        <p style="text-align: center; color: var(--secondary); margin-bottom: 1rem;">Scan QR Code</p>
                        <div class="upi-qr">
                            📱
                        </div>
                        <p style="text-align: center; color: var(--secondary); font-size: 0.85rem;">Scan with any UPI app</p>
                    </div>

                    <!-- Net Banking Details -->
                    <div class="payment-details netbanking-details" id="netbankingDetails">
                        <h4 style="margin-bottom: 1rem; color: var(--primary);">Select Your Bank</h4>
                        <div class="form-group">
                            <label>Choose Bank</label>
                            <select name="bank">
                                <option value="">Select a bank</option>
                                <option value="sbi">State Bank of India</option>
                                <option value="hdfc">HDFC Bank</option>
                                <option value="icici">ICICI Bank</option>
                                <option value="axis">Axis Bank</option>
                                <option value="kotak">Kotak Mahindra Bank</option>
                                <option value="pnb">Punjab National Bank</option>
                                <option value="bob">Bank of Baroda</option>
                            </select>
                        </div>
                        <p style="color: var(--secondary); font-size: 0.85rem; margin-top: 1rem;">
                            You'll be redirected to your bank's website to complete the payment.
                        </p>
                    </div>

                    <!-- Wallet Details -->
                    <div class="payment-details wallet-details" id="walletDetails">
                        <h4 style="margin-bottom: 1rem; color: var(--primary); grid-column: 1/-1;">Choose Digital Wallet</h4>
                        <div class="wallet-option" data-wallet="paytm">
                            <div style="font-size: 2rem; margin-bottom: 0.5rem;">💙</div>
                            <div style="font-weight: 600;">Paytm</div>
                        </div>
                        <div class="wallet-option" data-wallet="phonepe">
                            <div style="font-size: 2rem; margin-bottom: 0.5rem;">💜</div>
                            <div style="font-weight: 600;">PhonePe</div>
                        </div>
                        <div class="wallet-option" data-wallet="googlepay">
                            <div style="font-size: 2rem; margin-bottom: 0.5rem;">🔵</div>
                            <div style="font-weight: 600;">Google Pay</div>
                        </div>
                        <div class="wallet-option" data-wallet="amazonpay">
                            <div style="font-size: 2rem; margin-bottom: 0.5rem;">🟡</div>
                            <div style="font-weight: 600;">Amazon Pay</div>
                        </div>
                    </div>

                    <!-- COD Details -->
                    <div class="payment-details cod-details" id="codDetails">
                        <h4 style="margin-bottom: 1rem; color: var(--primary);">Cash on Delivery</h4>
                        <p style="margin-bottom: 0.5rem;">💵 Pay when you receive your order</p>
                        <p style="font-size: 0.85rem; color: var(--secondary);">
                            Please keep exact change handy. ₹50 extra fee may apply for COD orders.
                        </p>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-place-order">Place Order - <?php echo formatPrice($total); ?></button>
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

<script>
// Payment Method Switching
document.addEventListener('DOMContentLoaded', function() {
    const paymentOptions = document.querySelectorAll('.payment-option');
    const paymentDetails = document.querySelectorAll('.payment-details');
    
    paymentOptions.forEach(option => {
        option.addEventListener('click', function() {
            const paymentType = this.getAttribute('data-payment');
            const radio = this.querySelector('input[type="radio"]');
            
            // Update radio
            radio.checked = true;
            
            // Remove selected class from all options
            paymentOptions.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
            
            // Hide all payment details
            paymentDetails.forEach(detail => detail.classList.remove('active'));
            
            // Show selected payment details
            const selectedDetail = document.getElementById(paymentType + 'Details');
            if (selectedDetail) {
                selectedDetail.classList.add('active');
            }
        });
    });
    
    // Set initial state
    const checkedOption = document.querySelector('.payment-option input[type="radio"]:checked');
    if (checkedOption) {
        checkedOption.closest('.payment-option').classList.add('selected');
    }
});

// Shipping Method Visual Highlight
document.addEventListener('DOMContentLoaded', function() {
    const shippingOptions = document.querySelectorAll('.shipping-option');
    
    shippingOptions.forEach(option => {
        option.addEventListener('click', function() {
            const radio = this.querySelector('input[type="radio"]');
            radio.checked = true;
            
            // Remove selected class from all
            shippingOptions.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
        });
    });
    
    // Set initial state
    const checkedShipping = document.querySelector('.shipping-option input[type="radio"]:checked');
    if (checkedShipping) {
        checkedShipping.closest('.shipping-option').classList.add('selected');
    }
});

// Wallet Selection
document.addEventListener('DOMContentLoaded', function() {
    const walletOptions = document.querySelectorAll('.wallet-option');
    
    walletOptions.forEach(option => {
        option.addEventListener('click', function() {
            walletOptions.forEach(opt => opt.classList.remove('selected'));
            this.classList.add('selected');
        });
    });
});

// Card Number Formatting
document.getElementById('cardNumber')?.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\s/g, '');
    let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
    e.target.value = formattedValue;
});

// Expiry Date Formatting
document.getElementById('cardExpiry')?.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length >= 2) {
        value = value.slice(0, 2) + '/' + value.slice(2, 4);
    }
    e.target.value = value;
});

// CVV - Numbers Only
document.getElementById('cardCVV')?.addEventListener('input', function(e) {
    e.target.value = e.target.value.replace(/\D/g, '');
});
</script>

<?php include 'includes/footer.php'; ?>
