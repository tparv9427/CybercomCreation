<?php
// Variables passed from CartController:
// $page_title, $cart_items, $pricing
/** @var callable $getCategory */
/** @var callable $formatPrice */
?>

<link rel="stylesheet" href="/assets/css/cart.css">




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
                    <?php
                    $item['category_name'] = $getCategory($item['product']['category_id'])['name'];
                    $item['formatted_price'] = formatPrice($item['product']['price']);
                    $item['formatted_total'] = formatPrice($item['total']);
                    include __DIR__ . '/../components/cart_item.php';
                    ?>
                <?php endforeach; ?>
            </div>

            <div class="cart-summary">
                <h3>Order Summary</h3>
                <div class="summary-row">
                    <span>Subtotal:</span>
                    <span id="summary-subtotal"><?php echo formatPrice($pricing['subtotal']); ?></span>
                </div>
                <div class="summary-row">
                    <span>Tax (18%):</span>
                    <span id="summary-tax"><?php echo formatPrice($estimated_totals['tax_on_items']); ?></span>
                </div>
                <div class="summary-row" style="border-bottom: 2px solid var(--border); margin-bottom: 1rem;">
                    <span style="font-weight: 700; color: var(--primary);">Cart:</span>
                    <span id="summary-cart-value"
                        style="font-weight: 700; color: var(--primary);"><?php echo formatPrice($estimated_totals['cart_value']); ?></span>
                </div>

                <div class="summary-row">
                    <span>Delivery Type:</span>
                    <span id="summary-delivery-type"
                        style="text-transform: capitalize; color: var(--accent); font-weight: 500;">
                        <?php echo $shipping_category; ?> Shipping
                    </span>
                </div>

                <div class="summary-total">
                    <span>Estimated Total:</span>
                    <span id="summary-estimated-total">
                        <?php echo formatPrice($estimated_totals['min']) . ' - ' . formatPrice($estimated_totals['max']); ?>
                    </span>
                </div>

                <div class="cart-actions">
                    <button class="btn btn-primary" onclick="window.location.href='checkout.php'">Checkout</button>
                    <button class="btn btn-outline" onclick="window.location.href='index.php'">Continue Shopping</button>
                </div>
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

    <?php if (isset($saved_items) && count($saved_items) > 0): ?>
        <div class="saved-items-section" style="margin-top: 3rem;">
            <h3 style="margin-bottom: 1.5rem; color: var(--primary);">Saved for Later (<?php echo count($saved_items); ?>)
            </h3>
            <div class="cart-items">
                <?php foreach ($saved_items as $item): ?>
                    <?php
                    $item['formatted_price'] = formatPrice($item['product']['price']);
                    include __DIR__ . '/../components/saved_item.php';
                    ?>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>
</div>