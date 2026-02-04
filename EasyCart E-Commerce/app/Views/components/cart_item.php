<?php
/** @var array $item */
if (!isset($item) || !isset($item['product'])) {
    return;
}

$productId = $item['product']['id'];
$productName = $item['product']['name'] ?? 'Unknown Product';
$productIcon = $item['product']['icon'] ?? '';
$categoryName = $item['category_name'] ?? 'Others';
$shippingType = $item['shipping_type'] ?? 'Express Shipping';
$price = $item['formatted_price'] ?? '$0.00';
$itemTotal = $item['formatted_total'] ?? '$0.00';
$quantity = $item['quantity'] ?? 1;
$stock = $item['product']['stock'] ?? 999;
?>
<div class="cart-item" data-product-id="<?php echo $productId; ?>">
    <div class="item-image" onclick="window.location.href='/product/<?php echo $productId; ?>'">
        <?php echo $productIcon; ?>
    </div>
    <div class="item-details">
        <h3 class="item-name">
            <?php echo $productName; ?>
        </h3>
        <p class="item-category">
            <?php echo $categoryName; ?>
        </p>
        <p class="item-shipping-type" style="font-size: 0.85rem; color: #e67e22; margin-bottom: 0.25rem;">
            <span style="margin-right: 4px;">🚚</span><?php echo $shippingType; ?>
        </p>
        <p class="item-price">
            <?php echo $price; ?>
        </p>
        <button class="btn-link" onclick="saveForLater(<?php echo $productId; ?>, event)"
            style="color: var(--primary); margin-top: 0.5rem; display: block; background: none; border: none; padding: 0; cursor: pointer; text-decoration: underline;">Save
            for Later</button>
    </div>
    <div class="item-quantity">
        <div class="quantity-controls">
            <?php if ($quantity == 1): ?>
                <button class="quantity-btn delete-btn-sm" id="btn-decrease-<?php echo $productId; ?>"
                    onclick="removeFromCart(<?php echo $productId; ?>)">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 6h18"></path>
                        <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                        <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                    </svg>
                </button>
            <?php else: ?>
                <button class="quantity-btn" id="btn-decrease-<?php echo $productId; ?>"
                    onclick="decreaseCartQuantity(<?php echo $productId; ?>)">−</button>
            <?php endif; ?>
            <input type="number" class="quantity-input" id="qty-<?php echo $productId; ?>"
                value="<?php echo $quantity; ?>" data-old-value="<?php echo $quantity; ?>" min="1"
                max="<?php echo $stock; ?>" oninput="validateCartQuantity(<?php echo $productId; ?>, this)">
            <button class="quantity-btn" id="btn-increase-<?php echo $productId; ?>"
                onclick="increaseCartQuantity(<?php echo $productId; ?>)" <?php echo ($quantity >= $stock) ? 'disabled' : ''; ?>>+</button>
        </div>
        <div class="item-total" style="margin-top: 0.5rem; text-align: center; font-size: 0.95rem;">
            <?php echo $itemTotal; ?>
        </div>
    </div>
    <button class="item-remove" onclick="removeFromCart(<?php echo $productId; ?>)">×</button>
</div>