<?php
/**
 * Reusable Product Row Item Component - Amazon Style
 */
?>
<div class="product-row-item" onclick="window.location.href='product.php?id=<?php echo $product['id']; ?>'">
    <!-- Left: Image -->
    <div class="product-row-image-container">
        <div class="product-row-image">
            <?php echo $product['icon']; ?>
        </div>
    </div>

    <!-- Center: Info -->
    <div class="product-row-info">
        <div class="product-row-title">
            <?php echo $product['name']; ?>
        </div>

        <div class="product-row-rating">
            <span class="stars">
                <?php
                $fullStars = floor($product['rating']);
                $halfStar = ($product['rating'] - $fullStars) >= 0.5;
                for ($i = 0; $i < $fullStars; $i++)
                    echo '★';
                if ($halfStar)
                    echo '☆';
                for ($i = ceil($product['rating']); $i < 5; $i++)
                    echo '☆';
                ?>
            </span>
            <span class="rating-count"><?php echo $product['reviews_count']; ?></span>
        </div>

        <div class="product-row-sales-text">1K+ bought in past month</div>

        <div class="product-row-price-block">
            <div class="price-row">
                <span class="currency-symbol">$</span>
                <span class="price-whole"><?php echo floor($product['price']); ?></span>
                <span
                    class="price-fraction"><?php echo sprintf('%02d', ($product['price'] - floor($product['price'])) * 100); ?></span>
            </div>
            <?php if ($product['original_price'] > $product['price']): ?>
                <span class="original-price">M.R.P.: <span
                        style="text-decoration: line-through;"><?php echo formatPrice($product['original_price']); ?></span></span>
                <span class="discount-text">(<?php echo $product['discount_percent']; ?>% off)</span>
            <?php endif; ?>
        </div>

        <?php $shipping_type = ($product['price'] >= 300) ? 'Freight Shipping' : 'Express Shipping'; ?>
        <div class="product-shipping-type" style="font-size: 0.8rem; color: #565959; margin-top: 4px;">
            <span style="margin-right: 4px;">🚚</span>
            <?php echo $shipping_type; ?>
        </div>

        <?php if ($product['stock'] < 10 && $product['stock'] > 0): ?>
            <div class="stock-urgency">Only <?php echo $product['stock']; ?> left in stock.</div>
        <?php endif; ?>
    </div>

    <!-- Right: Action -->
    <div class="product-row-action">
        <button class="btn-amazon-primary" onclick="addToCart(<?php echo $product['id']; ?>, event)">Add to
            cart</button>
        <div class="more-buying-choices">
            <a href="product.php?id=<?php echo $product['id']; ?>">More Buying Choices</a>
        </div>
        <button class="save-btn" onclick="saveForLater(<?php echo $product['id']; ?>, event)" title="Save for Later"
            style="width: fit-content; background: white; border: 1px solid #ddd; border-radius: 4px; color: #555; cursor: pointer; transition: all 0.2s; padding: 2px;">
            Save for later
        </button>
    </div>
</div>