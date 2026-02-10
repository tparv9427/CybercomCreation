<?php
/**
 * Reusable Product Row Item Component - Amazon Style
 */
?>
<div class="product-row-item" onclick="window.location.href='<?php echo product_url($product); ?>'">
    <!-- Left: Image -->
    <div class="product-row-image-container">
        <div class="product-row-image">
            <?php echo $product['icon']; ?>
        </div>
    </div>

    <!-- Center: Info -->
    <!-- Center: Info & Details -->
    <div class="product-row-info">
        <a href="<?php echo product_url($product); ?>" class="product-row-title">
            <?php echo $product['name']; ?>
        </a>

        <div class="product-row-rating">
            <span class="stars" style="color: #ffa41c;">
                <?php
                $rating = $product['rating'] ?? 0;
                $fullStars = floor($rating);
                $halfStar = ($rating - $fullStars) >= 0.5;
                for ($i = 0; $i < $fullStars; $i++)
                    echo '★';
                if ($halfStar)
                    echo '☆'; // In a real app, use a half-star icon
                for ($i = ceil($rating + ($halfStar ? 0.5 : 0)); $i < 5; $i++)
                    echo '☆';
                ?>
            </span>
            <span class="rating-count"
                style="color: #007185; margin-left: 5px;"><?php echo $product['reviews_count'] ?? 0; ?></span>
        </div>

        <div class="product-row-sales-text" style="font-size: 0.8rem; color: #565959; margin-bottom: 4px;">1K+ bought in
            past month</div>

        <div class="product-row-price-block" style="display: flex; align-items: baseline; gap: 4px;">
            <span class="currency-symbol" style="font-size: 0.75rem; vertical-align: top;">$</span>
            <span class="price-whole"
                style="font-size: 1.5rem; font-weight: 500;"><?php echo floor($product['price']); ?></span>
            <span class="price-fraction"
                style="font-size: 0.75rem; vertical-align: top;"><?php echo sprintf('%02d', ($product['price'] - floor($product['price'])) * 100); ?></span>

            <?php if (($product['original_price'] ?? 0) > $product['price']): ?>
                <span class="original-price" style="color: #565959; font-size: 0.8rem; margin-left: 5px;">
                    M.R.P.: <span
                        style="text-decoration: line-through;"><?php echo formatPrice($product['original_price']); ?></span>
                </span>
                <span class="discount-text"
                    style="color: #CC0C39; font-size: 0.8rem;">(<?php echo $product['discount_percent']; ?>% off)</span>
            <?php endif; ?>
        </div>

        <?php $shipping_type = ($product['price'] >= 300) ? 'Freight Shipping' : 'Express Shipping'; ?>
        <div class="product-shipping-type" style="font-size: 0.8rem; color: #565959; margin-top: 4px;">
            <span style="font-weight: bold;">Prime</span> <?php echo $shipping_type; ?>
        </div>
    </div>

    <!-- Right: Action -->
    <div class="product-row-action">
        <button class="btn-amazon-primary" onclick="addToCart(<?php echo $product['id']; ?>, event)">Add to
            cart</button>
        <div class="more-buying-choices">
            <a href="<?php echo product_url($product); ?>">More Buying Choices</a>
        </div>
        <button class="save-btn" onclick="saveForLater(<?php echo $product['id']; ?>, event)" title="Save for Later"
            style="width: fit-content; background: white; border: 1px solid #ddd; border-radius: 4px; color: #555; cursor: pointer; transition: all 0.2s; padding: 2px;">
            Save for later
        </button>
    </div>
</div>