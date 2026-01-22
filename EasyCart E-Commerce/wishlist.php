<?php
require_once 'includes/config.php';
$page_title = 'My Wishlist';
$wishlist_products = [];
foreach ($_SESSION['wishlist'] as $product_id) {
    $product = getProduct($product_id);
    if ($product) $wishlist_products[] = $product;
}
include 'includes/header.php';
?>
<link rel="stylesheet" href="assets/css/cart.css">
<div class="breadcrumb"><a href="index.php">Home</a> / Wishlist</div>
<div class="container">
    <div class="section-header">
        <h2 class="section-title">My Wishlist</h2>
        <p class="section-subtitle"><?php echo count($wishlist_products); ?> item(s) saved</p>
    </div>
    <?php if (count($wishlist_products) > 0): ?>
        <div class="product-grid">
            <?php foreach ($wishlist_products as $product): ?>
                <div class="product-card" onclick="window.location.href='product.php?id=<?php echo $product['id']; ?>'">
                    <div class="product-image"><?php echo $product['icon']; ?>
                        <button class="wishlist-btn active" onclick="toggleWishlist(<?php echo $product['id']; ?>, event)">❤️</button>
                    </div>
                    <div class="product-info">
                        <div class="product-category"><?php echo getCategory($product['category_id'])['name']; ?></div>
                        <div class="product-name"><?php echo $product['name']; ?></div>
                        <div class="product-price"><?php echo formatPrice($product['price']); ?></div>
                        <button class="btn btn-primary" style="margin-top: 1rem; width: 100%;" onclick="addToCart(<?php echo $product['id']; ?>, event)">Add to Cart</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="empty-cart">
            <div class="empty-icon">🤍</div>
            <h3>Your wishlist is empty</h3>
            <p>Save items you love for later!</p>
            <a href="products.php" class="btn">Start Shopping</a>
        </div>
    <?php endif; ?>
</div>
<?php include 'includes/footer.php'; ?>
