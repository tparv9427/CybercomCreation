<?php
// Variables passed from CheckoutController:
// $page_title, $cart_items, $pricing, $user (optional for pre-filling form)
?>

<link rel="stylesheet" href="/assets/css/checkout.css">

<div class="breadcrumb">
    <a href="/">Home</a> / <a href="/cart">Cart</a> / Checkout
</div>

<div class="container">
    <div class="section-header">
        <h2 class="section-title">Checkout</h2>
        <p class="section-subtitle">Complete your purchase</p>
    </div>

    <div class="checkout-layout">
        <div class="checkout-form">
            <form method="POST" id="checkoutForm">
                <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                <!-- Billing Address Section -->
                <div class="address-card">
                    <div class="card-header">
                        <h3>Billing Address</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-row compact-row">
                            <div class="form-group floating-label">
                                <input type="text" name="billing_name" required value="<?php echo $_SESSION['user_name'] ?? ''; ?>" id="billing_name" autocomplete="name">
                                <label for="billing_name">Full Name *</label>
                            </div>
                            <div class="form-group floating-label">
                                <input type="tel" name="billing_phone" required maxlength="10" id="billing_phone" autocomplete="tel">
                                <label for="billing_phone">Phone Number *</label>
                            </div>
                        </div>
                        <div class="form-group floating-label compact-row">
                            <input type="text" name="billing_address" required id="billing_address" autocomplete="street-address">
                            <label for="billing_address">Address Line *</label>
                        </div>
                        <div class="form-row compact-row">
                            <div class="form-group floating-label">
                                <input type="text" name="billing_city" required id="billing_city" autocomplete="address-level2">
                                <label for="billing_city">City *</label>
                            </div>
                            <div class="form-group floating-label">
                                <input type="text" name="billing_zip" required maxlength="6" id="billing_zip" autocomplete="postal-code">
                                <label for="billing_zip">Zip Code *</label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Shipping Address Toggle -->
                <div class="shipping-toggle">
                    <label class="checkbox-container">
                        <input type="checkbox" id="same_as_billing" name="same_as_billing" checked onchange="toggleShippingAddress()">
                        <span class="checkmark"></span>
                        <span class="toggle-text">Shipping address same as billing address</span>
                    </label>
                </div>

                <!-- Shipping Address Section (Hidden by default) -->
                <div class="address-card" id="shipping-address-section" style="display: none;">
                    <div class="card-header">
                        <h3>Shipping Address</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-row compact-row">
                            <div class="form-group floating-label">
                                <input type="text" name="shipping_name" id="shipping_name" autocomplete="name">
                                <label for="shipping_name">Full Name *</label>
                            </div>
                            <div class="form-group floating-label">
                                <input type="tel" name="shipping_phone" maxlength="10" id="shipping_phone" autocomplete="tel">
                                <label for="shipping_phone">Phone Number *</label>
                            </div>
                        </div>
                        <div class="form-group floating-label compact-row">
                            <input type="text" name="shipping_address" id="shipping_address" autocomplete="street-address">
                            <label for="shipping_address">Address Line *</label>
                        </div>
                        <div class="form-row compact-row">
                            <div class="form-group floating-label">
                                <input type="text" name="shipping_city" id="shipping_city" autocomplete="address-level2">
                                <label for="shipping_city">City *</label>
                            </div>
                            <div class="form-group floating-label">
                                <input type="text" name="shipping_zip" maxlength="6" id="shipping_zip" autocomplete="postal-code">
                                <label for="shipping_zip">Zip Code *</label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-section">
                    <h3>Shipping Method</h3>
                    
                    <!-- Category 1: Express Shipping -->
                    <div class="shipping-category" id="express-category">
                        <h4>Express Shipping</h4>
                        <label class="radio-option <?php echo $allowed_category !== 'express' ? 'disabled' : ''; ?>">
                            <div style="display: flex; align-items: flex-start;">
                                <input type="radio" name="shipping" value="standard" style="margin-top: 4px;"
                                       <?php echo $allowed_category !== 'express' ? 'disabled' : ''; ?>
                                       <?php echo $shipping_method === 'standard' ? 'checked' : ''; ?>>
                                <div>
                                    <span style="display: block; font-weight: 500;">Standard Shipping (3-5 days) - $40.00</span>
                                    <small style="display: block; color: var(--text-secondary); margin-top: 4px;">Reliable delivery via standard ground carrier. Best for non-urgent orders.</small>
                                </div>
                            </div>
                        </label>
                        <label class="radio-option <?php echo $allowed_category !== 'express' ? 'disabled' : ''; ?>">
                            <div style="display: flex; align-items: flex-start;">
                                <input type="radio" name="shipping" value="express" style="margin-top: 4px;"
                                       <?php echo $allowed_category !== 'express' ? 'disabled' : ''; ?>
                                       <?php echo $shipping_method === 'express' ? 'checked' : ''; ?>>
                                <div>
                                    <span style="display: block; font-weight: 500;">Express Shipping (1-2 days) - $80.00 or 10% (whichever is lower)</span>
                                    <small style="display: block; color: var(--text-secondary); margin-top: 4px;">Priority air shipping for time-sensitive orders. Live tracking included.</small>
                                </div>
                            </div>
                        </label>
                    </div>

                    <!-- Category 2: Freight Shipping -->
                    <div class="shipping-category" id="freight-category">
                        <h4>Freight Shipping</h4>
                        <label class="radio-option <?php echo $allowed_category !== 'freight' ? 'disabled' : ''; ?>">
                            <div style="display: flex; align-items: flex-start;">
                                <input type="radio" name="shipping" value="white_glove" style="margin-top: 4px;"
                                       <?php echo $allowed_category !== 'freight' ? 'disabled' : ''; ?>
                                       <?php echo $shipping_method === 'white_glove' ? 'checked' : ''; ?>>
                                <div>
                                    <span style="display: block; font-weight: 500;">White Glove Delivery (Scheduled) - $150.00 or 5% (whichever is lower)</span>
                                    <small style="display: block; color: var(--text-secondary); margin-top: 4px;">Premium service. Includes inside delivery, unpacking, assembly, and debris removal.</small>
                                </div>
                            </div>
                        </label>
                        <label class="radio-option <?php echo $allowed_category !== 'freight' ? 'disabled' : ''; ?>">
                            <div style="display: flex; align-items: flex-start;">
                                <input type="radio" name="shipping" value="freight" style="margin-top: 4px;"
                                       <?php echo $allowed_category !== 'freight' ? 'disabled' : ''; ?>
                                       <?php echo $shipping_method === 'freight' ? 'checked' : ''; ?>>
                                <div>
                                    <span style="display: block; font-weight: 500;">Freight Shipping (Bulk Orders) - 3% or $200 (whichever is higher)</span>
                                    <small style="display: block; color: var(--text-secondary); margin-top: 4px;">Economical palletized delivery to curbside or dock. Recipient must be present.</small>
                                </div>
                            </div>
                        </label>
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
                        <p style="text-align: center; color: var(--secondary); font-size: 0.85rem;">Scan with any UPI
                            app</p>
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
                        <h4 style="margin-bottom: 1rem; color: var(--primary); grid-column: 1/-1;">Choose Digital Wallet
                        </h4>
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

                <div class="checkout-actions" style="display: flex; gap: 1rem; align-items: center; margin-top: 2rem;">
                    <button type="button" class="btn btn-outline btn-cancel-order" onclick="confirmCancelOrder()"
                        style="border: 1px solid #ef4444; color: #ef4444; background: white;">Cancel Order</button>
                    <button type="submit" class="btn btn-primary btn-place-order" style="flex: 1;">Place Order -
                        <?php echo formatPrice($pricing['total']); ?></button>
                </div>
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
                    <span id="subtotal"><?php echo formatPrice($pricing['subtotal']); ?></span>
                </div>
                
                <?php
                // Check for applied coupon in session
                $coupon_data = $_SESSION['applied_coupon'] ?? null;
                $final_total = $pricing['total'];
                $discount_amount = 0;

                if ($coupon_data) {
                    // Recalculate discount based on current subtotal
                    $discount_amount = ($pricing['subtotal'] * $coupon_data['percent']) / 100;
                    $final_total = $pricing['total'] - $discount_amount;
                }
                ?>

                <?php if ($coupon_data): ?>
                    <div id="discount-row-container">
                        <div class="summary-row" style="color: #4caf50; margin-bottom: 0.5rem;">
                            <span>Discount (<?php echo $coupon_data['percent']; ?>%):</span>
                            <span>-<?php echo formatPrice($discount_amount); ?></span>
                        </div>
                        <button type="button" onclick="removeCoupon()" style="width: 100%; padding: 0.5rem; background: #ffebee; color: #d32f2f; border: 1px solid #ffcdd2; border-radius: 6px; cursor: pointer; font-size: 0.85rem; font-weight: 500; margin-bottom: 1rem; transition: all 0.2s;">
                            Remove Coupon
                        </button>
                    </div>
                <?php endif; ?>

                <div class="summary-row">
                    <span>Shipping:</span>
                    <span id="shipping"><?php echo formatPrice($pricing['shipping']); ?></span>
                </div>
                <div class="summary-row" id="payment-fee-row" style="display: <?php echo $payment_fee > 0 ? 'flex' : 'none'; ?>;">
                    <span>Payment Fee:</span>
                    <span id="payment-fee"><?php echo formatPrice($payment_fee); ?></span>
                </div>
                <div class="summary-row">
                    <span>Tax (18%):</span>
                    <span id="tax"><?php echo formatPrice($pricing['tax']); ?></span>
                </div>
                <div class="summary-total">
                    <span>Total:</span>
                    <span id="total"><?php echo formatPrice($final_total); ?></span>
                </div>
            </div>
            
            <!-- Coupon Code Section -->
            <div class="coupon-section" style="margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--border);">
                <h4 style="font-size: 1rem; color: var(--primary); margin-bottom: 0.75rem;">Have a Coupon?</h4>
                <div style="display: flex; gap: 0.5rem;">
                    <input type="text" id="couponCode" 
                           placeholder="Enter code" 
                           value="<?php echo $coupon_data ? htmlspecialchars($coupon_data['code']) : ''; ?>"
                           <?php echo $coupon_data ? 'disabled' : ''; ?>
                           style="flex: 1; padding: 0.6rem; border: 1.5px solid var(--border); border-radius: 6px; font-size: 0.9rem;">
                    <button type="button" 
                            onclick="applyCoupon()" 
                            <?php echo $coupon_data ? 'disabled' : ''; ?>
                            style="padding: 0.6rem 1rem; background: var(--primary); color: white; border: none; border-radius: 6px; font-weight: 500; cursor: pointer; transition: background 0.2s;">
                        <?php echo $coupon_data ? 'Applied' : 'Apply'; ?>
                    </button>
                </div>
                <div id="couponMessage" style="font-size: 0.85rem; margin-top: 0.5rem; display: <?php echo $coupon_data ? 'block' : 'none'; ?>; color: #4caf50;">
                    <?php echo $coupon_data ? 'Coupon applied successfully!' : ''; ?>
                </div>
            </div>
            
            <script>
                // Handle Enter key for coupon input
                document.getElementById('couponCode').addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        applyCoupon();
                    }
                });

                function removeCoupon() {
                    const btn = document.querySelector('.coupon-section button');
                    
                    fetch('/checkout/coupon', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-CSRF-TOKEN': getCsrfToken()
                        },
                        body: 'action=remove'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Remove Discount Row Container
                            const discountContainer = document.getElementById('discount-row-container');
                            if (discountContainer) discountContainer.remove();
                            
                            // Reset Total
                            document.querySelector('#total').textContent = data.new_total;
                            
                            // Reset Input State
                            const codeInput = document.getElementById('couponCode');
                            codeInput.value = '';
                            codeInput.disabled = false;
                            
                            // Reset Button State
                            btn.disabled = false;
                            btn.textContent = 'Apply';
                            
                            // Hide Message
                            const messageEl = document.getElementById('couponMessage');
                            messageEl.style.display = 'none';
                        }
                    })
                    .catch(error => console.error('Error removing coupon:', error));
                }

                function applyCoupon() {
                    const codeInput = document.getElementById('couponCode');
                    const code = codeInput.value.trim();
                    const messageEl = document.getElementById('couponMessage');
                    const btn = document.querySelector('.coupon-section button');
                    
                    // Reset state
                    messageEl.style.display = 'none';
                    codeInput.classList.remove('error');
                    
                    if (!code) {
                        messageEl.textContent = 'Please enter a coupon code';
                        messageEl.style.color = '#ef5350';
                        messageEl.style.display = 'block';
                        codeInput.classList.add('error');
                        return;
                    }
                    
                    const originalText = btn.textContent;
                    btn.disabled = true;
                    btn.textContent = 'Applying...';
                    
                    fetch('/checkout/coupon', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-CSRF-TOKEN': getCsrfToken()
                        },
                        body: 'code=' + encodeURIComponent(code)
                    })
                    .then(response => response.json())
                    .then(data => {
                        btn.disabled = false;
                        btn.textContent = originalText;
                        
                        if (data.success) {
                            messageEl.textContent = data.message;
                            messageEl.style.color = '#4caf50';
                            messageEl.style.display = 'block';
                            
                            // Insert Discount Row
                            const summaryTotals = document.querySelector('.summary-totals');
                            const existingContainer = document.getElementById('discount-row-container');
                            
                            if (existingContainer) existingContainer.remove();
                            
                            const discountContainer = document.createElement('div');
                            discountContainer.id = 'discount-row-container';
                            discountContainer.innerHTML = `
                                <div class="summary-row" style="color: #4caf50; margin-bottom: 0.5rem;">
                                    <span>Discount (${data.discount_percent}%):</span>
                                    <span>-${data.discount_amount}</span>
                                </div>
                                <button type="button" onclick="removeCoupon()" style="width: 100%; padding: 0.5rem; background: #ffebee; color: #d32f2f; border: 1px solid #ffcdd2; border-radius: 6px; cursor: pointer; font-size: 0.85rem; font-weight: 500; margin-bottom: 1rem; transition: all 0.2s;">
                                    Remove Coupon
                                </button>
                            `;
                            
                            // Insert after Subtotal
                            const subtotalRow = document.getElementById('subtotal').closest('.summary-row');
                            if (subtotalRow) {
                                subtotalRow.insertAdjacentElement('afterend', discountContainer);
                            }
                            
                            // Update Total
                            document.querySelector('#total').textContent = data.new_total;
                            
                            // Disable input and button to prevent re-submit
                            codeInput.disabled = true;
                            btn.textContent = 'Applied';
                            btn.disabled = true;
                        } else {
                            messageEl.textContent = data.message;
                            messageEl.style.color = '#ef5350';
                            messageEl.style.display = 'block';
                            codeInput.classList.add('error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        btn.disabled = false;
                        btn.textContent = originalText;
                        messageEl.textContent = 'An error occurred. Please try again.';
                        messageEl.style.color = '#ef5350';
                        messageEl.style.display = 'block';
                    });
                }
            </script>
        </div>
    </div>
</div>

<style>
.shipping-category {
    margin-bottom: 1.5rem;
    padding: 1.5rem;
    border: 1px solid var(--border);
    border-radius: 8px;
    background: var(--bg-primary);
}

.shipping-category h4 {
    margin: 0 0 1rem 0;
    color: var(--primary);
    font-size: 1.1rem;
    font-weight: 600;
}

.radio-option {
    display: block;
    padding: 1rem;
    margin-bottom: 0.5rem;
    border: 2px solid var(--border);
    border-radius: 8px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
}

.radio-option:last-child {
    margin-bottom: 0;
}

.radio-option:hover:not(.disabled) {
    border-color: var(--primary);
    background: var(--bg-secondary);
}

.radio-option.disabled {
    opacity: 0.5;
    cursor: not-allowed;
    background: #f5f5f5;
}

.radio-option input[type="radio"] {
    margin-right: 0.75rem;
    cursor: pointer;
}

.radio-option.disabled input[type="radio"] {
    cursor: not-allowed;
}

.radio-option span {
    font-size: 0.95rem;
}
</style>

<script>
    function confirmCancelOrder() {
        showConfirmationModal({
            title: 'Cancel Order',
            message: 'Are you sure you want to cancel this order? Item(s) will be returned to your cart.',
            confirmText: 'Yes, Cancel',
            onConfirm: function() {
                window.location.href = '/checkout?action=cancel';
            }
        });
    }

    // Payment Method Switching
    document.addEventListener('DOMContentLoaded', function () {
        const paymentSelect = document.getElementById('payment-select');
        const paymentDetails = document.querySelectorAll('.payment-details');

        if (paymentSelect) {
            // Initialize payment persistence
            const savedPayment = localStorage.getItem('selectedPayment');

            if (savedPayment) {
                // Validate if the saved option exists
                const optionExists = Array.from(paymentSelect.options).some(opt => opt.value === savedPayment);
                if (optionExists) {
                    paymentSelect.value = savedPayment;
                }
            }

            // Trigger initial change to show correct details
            const triggerChange = () => {
                const paymentType = paymentSelect.value;

                // Hide all payment details
                paymentDetails.forEach(detail => detail.classList.remove('active'));

                // Show selected payment details
                const selectedDetail = document.getElementById(paymentType + 'Details');
                if (selectedDetail) {
                    selectedDetail.classList.add('active');
                }
            };

            // Run once on load
            triggerChange();

            // Sync payment method to server on page load
            const syncPaymentMethod = () => {
                const savedPayment = localStorage.getItem('selectedPayment');
                const currentPayment = paymentSelect.value;
                
                // If there's a saved payment different from default, sync to server
                if (savedPayment && savedPayment !== 'card') {
                    fetch('ajax_checkout.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-CSRF-TOKEN': getCsrfToken()
                        },
                        body: `action=pricing&payment=${savedPayment}&shipping=${document.querySelector('input[name="shipping"]:checked')?.value || 'standard'}`
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.pricing) {
                            // Update pricing display
                            const subtotalEl = document.getElementById('subtotal');
                            const shippingEl = document.getElementById('shipping');
                            const paymentFeeEl = document.getElementById('payment-fee');
                            const paymentFeeRow = document.getElementById('payment-fee-row');
                            const taxEl = document.getElementById('tax');
                            const totalEl = document.getElementById('total');

                            if (subtotalEl) subtotalEl.textContent = data.pricing.subtotal;
                            if (shippingEl) shippingEl.textContent = data.pricing.shipping;
                            if (taxEl) taxEl.textContent = data.pricing.tax;
                            if (totalEl) totalEl.textContent = data.pricing.total;

                            // Show/hide payment fee row
                            if (data.pricing.payment_fee) {
                                if (paymentFeeEl) paymentFeeEl.textContent = data.pricing.payment_fee;
                                if (paymentFeeRow) paymentFeeRow.style.display = 'flex';
                            } else {
                                if (paymentFeeRow) paymentFeeRow.style.display = 'none';
                            }
                        }
                    })
                    .catch(error => console.error('Error syncing payment method:', error));
                }
            };

            // Run sync after a short delay to ensure DOM is ready
            setTimeout(syncPaymentMethod, 100);

            paymentSelect.addEventListener('change', function () {
                const paymentType = this.value;

                // Save to localStorage
                localStorage.setItem('selectedPayment', paymentType);

                triggerChange();
            });
        }
    });

    // Floating Label Initialization
    document.addEventListener('DOMContentLoaded', function () {
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
            input.addEventListener('input', function () {
                checkInputValue(this);
            });

            // Handle autofill - check after a short delay
            setTimeout(() => {
                checkInputValue(input);
            }, 100);

            // Also check on animation start (Chrome autofill detection)
            input.addEventListener('animationstart', function (e) {
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
    document.addEventListener('DOMContentLoaded', function () {
        const walletOptions = document.querySelectorAll('.wallet-option');

        walletOptions.forEach(option => {
            option.addEventListener('click', function () {
                walletOptions.forEach(opt => opt.classList.remove('selected'));
                this.classList.add('selected');
            });
        });
    });

    // Card Number Formatting
    document.getElementById('cardNumber')?.addEventListener('input', function (e) {
        let value = e.target.value.replace(/\s/g, '');
        let formattedValue = value.match(/.{1,4}/g)?.join(' ') || value;
        e.target.value = formattedValue;
    });

    // Expiry Date Formatting
    document.getElementById('cardExpiry')?.addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, '');
        if (value.length >= 2) {
            value = value.slice(0, 2) + '/' + value.slice(2, 4);
        }
        e.target.value = value;
    });

    // CVV - Numbers Only
    document.getElementById('cardCVV')?.addEventListener('input', function (e) {
        e.target.value = e.target.value.replace(/\D/g, '');
    });

    // Toggle Shipping Address
    function toggleShippingAddress() {
        const checkbox = document.getElementById('same_as_billing');
        const shippingSection = document.getElementById('shipping-address-section');
        const shippingInputs = shippingSection.querySelectorAll('input');

        if (checkbox.checked) {
            shippingSection.style.display = 'none';
            // Remove required attribute from shipping fields
            shippingInputs.forEach(input => {
                input.removeAttribute('required');
                input.value = ''; // Optional: clear values
            });
        } else {
            shippingSection.style.display = 'block';
            // Add required attribute to shipping fields
            shippingInputs.forEach(input => {
                input.setAttribute('required', 'required');
            });
        }
    }

    // Initialize state on page load
    document.addEventListener('DOMContentLoaded', toggleShippingAddress);
</script>

