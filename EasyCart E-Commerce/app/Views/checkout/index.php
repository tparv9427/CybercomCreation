<?php
// Variables passed from CheckoutController:
// $page_title, $cart_items, $pricing, $user (optional for pre-filling form)
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
                        <div class="form-group floating-label">
                            <input type="text" name="name" required value="<?php echo $_SESSION['user_name'] ?? ''; ?>" id="name">
                            <label for="name">Full Name *</label>
                        </div>
                        <div class="form-group floating-label">
                            <input type="tel" name="phone" required maxlength="10" id="phone">
                            <label for="phone">Phone Number *</label>
                        </div>
                    </div>
                    <div class="form-group floating-label">
                        <input type="text" name="address" required id="address">
                        <label for="address">Address *</label>
                    </div>
                    <div class="form-row">
                        <div class="form-group floating-label">
                            <input type="text" name="city" required id="city">
                            <label for="city">City *</label>
                        </div>
                        <div class="form-group floating-label">
                            <input type="text" name="zip" required maxlength="6" id="zip">
                            <label for="zip">Zip Code *</label>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Shipping Method</h3>
                    <div class="form-group">
                        <select name="shipping" id="shipping-select" class="shipping-select">
                            <option value="standard" <?php echo ($shipping_method === 'standard') ? 'selected' : ''; ?>>Standard Shipping (3-5 days) - $40.00</option>
                            <option value="express" <?php echo ($shipping_method === 'express') ? 'selected' : ''; ?>>Express Shipping (1-2 days) - $80.00 or 10%</option>
                            <option value="white_glove" <?php echo ($shipping_method === 'white_glove') ? 'selected' : ''; ?>>White Glove Delivery (Scheduled) - $150.00 or 5%</option>
                            <option value="freight" <?php echo ($shipping_method === 'freight') ? 'selected' : ''; ?>>Freight Shipping (Bulk Orders) - 3% (Min $200)</option>
                        </select>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Payment Method</h3>
                    <div class="form-group">
                        <select name="payment" id="payment-select" class="payment-select">
                            <option value="card" selected>💳 Credit/Debit Card</option>
                            <option value="upi">📱 UPI</option>
                            <option value="netbanking">🏦 Net Banking</option>
                            <option value="wallet">👛 Digital Wallet</option>
                            <option value="cod">💵 Cash on Delivery</option>
                        </select>
                    </div>
                    
                    <!-- Card Details -->
                    <div class="payment-details card-details active" id="cardDetails">
                        <h4 style="margin-bottom: 1rem; color: var(--primary);">Enter Card Details</h4>
                        <div class="form-group floating-label">
                            <input type="text" id="cardNumber" maxlength="19">
                            <label for="cardNumber">Card Number</label>
                        </div>
                        <div class="form-row">
                            <div class="form-group floating-label">
                                <input type="text" id="cardExpiry" maxlength="5">
                                <label for="cardExpiry">Expiry Date (MM/YY)</label>
                            </div>
                            <div class="form-group floating-label">
                                <input type="text" id="cardCVV" maxlength="3">
                                <label for="cardCVV">CVV</label>
                            </div>
                        </div>
                    </div>

                    <!-- UPI Details -->
                    <div class="payment-details upi-details" id="upiDetails">
                        <h4 style="margin-bottom: 1rem; color: var(--primary);">Pay via UPI</h4>
                        <div class="form-group floating-label">
                            <input type="text" id="upiId">
                            <label for="upiId">Enter UPI ID</label>
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
                            Please keep exact change handy. $5 extra fee may apply for COD orders.
                        </p>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary btn-place-order">Place Order - <?php echo formatPrice($pricing['total']); ?></button>
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
                    <span><?php echo formatPrice($pricing['subtotal']); ?></span>
                </div>
                <div class="summary-row">
                    <span>Shipping:</span>
                    <span><?php echo formatPrice($pricing['shipping']); ?></span>
                </div>
                <div class="summary-row">
                    <span>Tax (18%):</span>
                    <span><?php echo formatPrice($pricing['tax']); ?></span>
                </div>
                <div class="summary-total">
                    <span>Total:</span>
                    <span><?php echo formatPrice($pricing['total']); ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Payment Method Switching
document.addEventListener('DOMContentLoaded', function() {
    const paymentSelect = document.getElementById('payment-select');
    const paymentDetails = document.querySelectorAll('.payment-details');
    
    if (paymentSelect) {
        paymentSelect.addEventListener('change', function() {
            const paymentType = this.value;
            
            // Hide all payment details
            paymentDetails.forEach(detail => detail.classList.remove('active'));
            
            // Show selected payment details
            const selectedDetail = document.getElementById(paymentType + 'Details');
            if (selectedDetail) {
                selectedDetail.classList.add('active');
            }
        });
    }
});

// Floating Label Initialization
document.addEventListener('DOMContentLoaded', function() {
    // Check all floating label inputs for pre-filled values
    const floatingInputs = document.querySelectorAll('.floating-label input');
    
    // Function to check if input has value
    function checkInputValue(input) {
        if (input.value && input.value.trim() !== '') {
            input.setAttribute('data-has-value', 'true');
        } else {
            input.removeAttribute('data-has-value');
        }
    }
    
    floatingInputs.forEach(input => {
        // Add placeholder attribute to enable :placeholder-shown pseudo-class
        if (!input.hasAttribute('placeholder')) {
            input.setAttribute('placeholder', ' ');
        }
        
        // Check initial value
        checkInputValue(input);
        
        // Update on input change
        input.addEventListener('input', function() {
            checkInputValue(this);
        });
        
        // Handle autofill - check after a short delay
        setTimeout(() => {
            checkInputValue(input);
        }, 100);
        
        // Also check on animation start (Chrome autofill detection)
        input.addEventListener('animationstart', function(e) {
            if (e.animationName === 'onAutoFillStart') {
                checkInputValue(this);
            }
        });
    });
    
    // Periodically check for autofill (fallback)
    setTimeout(() => {
        floatingInputs.forEach(input => checkInputValue(input));
    }, 500);
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

