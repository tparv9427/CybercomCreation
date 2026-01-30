<div class="cart-item" data-product-id="<?php echo $item['product']['id']; ?>">
    <div class="item-image" onclick="window.location.href='product.php?id=<?php echo $item['product']['id']; ?>'">
        <?php echo $item['product']['icon']; ?>
    </div>
    <div class="item-details">
        <h3 class="item-name">
            <?php echo $item['product']['name']; ?>
        </h3>
        <p class="item-category">
            <?php echo $item['category_name']; ?>
        </p>
        <p class="item-shipping-type" style="font-size: 0.85rem; color: #e67e22; margin-bottom: 0.25rem;">
            <span style="margin-right: 4px;">🚚</span><?php echo $item['shipping_type']; ?>
        </p>
        <p class="item-price">
            <?php echo $item['formatted_price']; ?>
        </p>
        <button class="btn-link" onclick="saveForLater(<?php echo $item['product']['id']; ?>, event)"
            style="color: var(--primary); margin-top: 0.5rem; display: block; background: none; border: none; padding: 0; cursor: pointer; text-decoration: underline;">Save
            for Later</button>
    </div>
    <div class="item-quantity">
        <div class="quantity-controls">
            <?php if ($item['quantity'] == 1): ?>
                <button class="quantity-btn delete-btn-sm" id="btn-decrease-<?php echo $item['product']['id']; ?>"
                    onclick="removeFromCart(<?php echo $item['product']['id']; ?>)">🗑</button>
            <?php else: ?>
                <button class="quantity-btn" id="btn-decrease-<?php echo $item['product']['id']; ?>"
                    onclick="decreaseCartQuantity(<?php echo $item['product']['id']; ?>)">−</button>
            <?php endif; ?>
            <input type="number" class="quantity-input" id="qty-<?php echo $item['product']['id']; ?>"
                value="<?php echo $item['quantity']; ?>" data-old-value="<?php echo $item['quantity']; ?>" min="1"
                max="<?php echo $item['product']['stock']; ?>"
                oninput="validateCartQuantity(<?php echo $item['product']['id']; ?>, this)">
            <button class="quantity-btn"
                onclick="increaseCartQuantity(<?php echo $item['product']['id']; ?>)">+</button>
        </div>
        <div class="item-total" style="margin-top: 0.5rem; text-align: center; font-size: 0.95rem;">
            <?php echo $item['formatted_total']; ?>
        </div>
    </div>
    <button class="item-remove" onclick="removeFromCart(<?php echo $item['product']['id']; ?>)">×</button>
</div>