<?php
// Variables passed from WishlistController:
// $page_title, $wishlist_items
?>
<link rel="stylesheet" href="/assets/css/cart.css">
<div class="breadcrumb"><a href="/">Home</a> / Wishlist</div>
<div class="container">
    <div class="section-header">
        <h2 class="section-title">My Wishlist</h2>
        <p class="section-subtitle"><?php echo count($wishlist_items); ?> item(s) saved</p>
    </div>
    <?php if (count($wishlist_items) > 0): ?>
        <div class="product-grid">
            <?php foreach ($wishlist_items as $product): ?>
                <?php include __DIR__ . '/../partials/product_card.php'; ?>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-cart">
            <div class="empty-icon">🤍</div>
            <h3>Your wishlist is empty</h3>
            <p>Save items you love for later!</p>
            <a href="/products" class="btn">Start Shopping</a>
        </div>
    <?php endif; ?>
</div>


