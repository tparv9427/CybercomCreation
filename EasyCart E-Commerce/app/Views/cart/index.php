<?php
// Variables passed from CartController:
// $page_title, $cart_items, $pricing
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
                    <div class="cart-item" data-product-id="<?php echo $item['product']['id']; ?>">
                        <div class="item-image" onclick="window.location.href='product.php?id=<?php echo $item['product']['id']; ?>'"><?php echo $item['product']['icon']; ?></div>
                        <div class="item-details">
                            <h3 class="item-name"><?php echo $item['product']['name']; ?></h3>
                            <p class="item-category"><?php echo $getCategory($item['product']['category_id'])['name']; ?></p>
                            <p class="item-price"><?php echo formatPrice($item['product']['price']); ?></p>
                        </div>
                        <div class="item-quantity">
                            <div class="quantity-controls">
                                <button class="quantity-btn" onclick="decreaseCartQuantity(<?php echo $item['product']['id']; ?>)">−</button>
                                <input type="number" class="quantity-input" id="qty-<?php echo $item['product']['id']; ?>" value="<?php echo $item['quantity']; ?>" min="1" max="<?php echo $item['product']['stock']; ?>" onchange="validateCartQuantity(<?php echo $item['product']['id']; ?>, this)">
                                <button class="quantity-btn" onclick="increaseCartQuantity(<?php echo $item['product']['id']; ?>)">+</button>
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
                    <span><?php echo formatPrice($pricing['subtotal']); ?></span>
                </div>
                <div class="summary-row">
                    <span>Shipping:</span>
                    <span><?php echo formatPrice($pricing['shipping']); ?></span>
                </div>
                <?php if ($pricing['item_count'] > 0): ?>
                    <div class="free-shipping-notice">
                        Standard Shipping: $40.00 (Flat Rate)
                    </div>
                <?php endif; ?>
                <div class="summary-row">
                    <span>Tax (18%):</span>
                    <span><?php echo formatPrice($pricing['tax']); ?></span>
                </div>
                <div class="summary-total">
                    <span>Total:</span>
                    <span><?php echo formatPrice($pricing['total']); ?></span>
                </div>
                <div class="cart-actions">
                    <a href="checkout.php" class="btn btn-primary">Checkout</a>
                    <a href="products.php" class="btn btn-outline">Continue</a>
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
</div>

