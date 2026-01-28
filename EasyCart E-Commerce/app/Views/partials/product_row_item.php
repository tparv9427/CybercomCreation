<?php
/**
 * Reusable Product Row Item Component
 * 
 * This partial renders a single product in row/list view format.
 * 
 * Required Variables:
 * @var array $product - Product data array with keys: id, name, price, original_price, 
 *                       icon, category_id, discount_percent, new, rating, reviews_count, description
 * 
 * Required Functions (from parent scope):
 * - $getCategory($id) - Returns category array
 * - formatPrice($amount) - Formats price with currency
 * - isInWishlist($productId) - Checks if product is in wishlist
 */
?>
<div class="product-row-item" onclick="window.location.href='product.php?id=<?php echo $product['id']; ?>'">
    <div class="product-row-image">
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
    <div class="product-row-info">
        <div class="product-category"><?php echo $getCategory($product['category_id'])['name']; ?></div>
        <div class="product-name"><?php echo $product['name']; ?></div>
        <div class="product-row-desc"><?php echo $product['description']; ?></div>
        <div class="product-rating">
            <span class="stars">
                <?php 
                $fullStars = floor($product['rating']);
                $halfStar = ($product['rating'] - $fullStars) >= 0.5;
                for ($i = 0; $i < $fullStars; $i++) echo '★';
                if ($halfStar) echo '☆';
                for ($i = ceil($product['rating']); $i < 5; $i++) echo '☆';
                ?>
            </span>
            <span>(<?php echo $product['reviews_count']; ?> reviews)</span>
        </div>
        <div class="product-price" style="margin-top: 1rem;">
            <?php echo formatPrice($product['price']); ?>
            <?php if ($product['original_price'] > $product['price']): ?>
                <span class="product-price-original"><?php echo formatPrice($product['original_price']); ?></span>
            <?php endif; ?>
        </div>
    </div>
</div>
