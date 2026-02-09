<?php
/**
 * Reusable Product Card Component - Minimalist Design
 * 
 * Shows only essential info (image, name, price) by default.
 * Reveals additional details (category, rating, add-to-cart) on hover.
 * 
 * Required Variables:
 * @var array $product - Product data array
 * 
 * Required Functions:
 * - $getCategory($id), formatPrice($amount), isInWishlist($productId)
 */
?>
<div class="product-card">
    <div class="product-image"
        onclick="window.location.href='<?php echo \EasyCart\Helpers\ViewHelper::productUrl($product); ?>'">
        <?php echo $product['icon']; ?>
        <?php if ($product['discount_percent'] > 0): ?>
            <span class="product-badge">-<?php echo $product['discount_percent']; ?>%</span>
        <?php elseif ($product['new']): ?>
            <span class="product-badge new">New</span>
        <?php endif; ?>
        <button class="wishlist-btn <?php echo isInWishlist($product['id']) ? 'active' : ''; ?>"
            onclick="toggleWishlist(<?php echo $product['id']; ?>, event)">
            <?php echo isInWishlist($product['id']) ? '❤️' : '🤍'; ?>
        </button>
    </div>
    <div class="product-info"
        onclick="window.location.href='<?php echo \EasyCart\Helpers\ViewHelper::productUrl($product); ?>'">
        <!-- Always visible -->
        <div class="product-name"><?php echo $product['name']; ?></div>
        <div class="product-price">
            <?php echo formatPrice($product['price']); ?>
            <?php if ($product['original_price'] > $product['price']): ?>
                <span class="product-price-original"><?php echo formatPrice($product['original_price']); ?></span>
            <?php endif; ?>
        </div>

        <!-- Revealed on hover -->
        <div class="product-details-hover">
            <div class="product-category">
                <?php
                $cat = $getCategory($product['category_id']);
                echo $cat ? $cat['name'] : 'Uncategorized';
                ?>
            </div>
            <div class="product-rating">
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
                <span class="review-count">(<?php echo $product['reviews_count']; ?>)</span>
            </div>
        </div>
    </div>
    <div class="card-actions" style="display: flex; gap: 0.5rem; width: 100%;">
        <button class="quick-add-btn add-to-cart-btn-<?php echo $product['id']; ?>"
            data-product-id="<?php echo $product['id']; ?>" onclick="addToCart(<?php echo $product['id']; ?>, event)"
            title="Add to Cart" style="flex: 1;">
            🛒 Add to Cart
        </button>
        <button class="save-btn" onclick="saveForLater(<?php echo $product['id']; ?>, event)" title="Save for Later"
            style="width: 40px; background: white; border: 1px solid #ddd; border-radius: 4px; color: #555; cursor: pointer; transition: all 0.2s;">
            🔖
        </button>
    </div>
</div>