<?php
require_once 'includes/config.php';

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$page_title = 'Search Results';

$search_results = [];
if ($query) {
    foreach ($products as $product) {
        if (stripos($product['name'], $query) !== false || 
            stripos($product['description'], $query) !== false ||
            stripos(getCategory($product['category_id'])['name'], $query) !== false) {
            $search_results[] = $product;
        }
    }
}

include 'includes/header.php';
?>

<div class="breadcrumb">
    <a href="index.php">Home</a> / Search Results
</div>

<div class="container">
    <div class="section-header">
        <h2 class="section-title">Search Results</h2>
        <p class="section-subtitle">
            <?php if ($query): ?>
                Found <?php echo count($search_results); ?> result(s) for "<?php echo htmlspecialchars($query); ?>"
            <?php else: ?>
                Please enter a search term
            <?php endif; ?>
        </p>
    </div>

    <?php if (count($search_results) > 0): ?>
        <div class="product-grid">
            <?php foreach ($search_results as $product): ?>
                <div class="product-card" onclick="window.location.href='product.php?id=<?php echo $product['id']; ?>'">
                    <div class="product-image">
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
                    <div class="product-info">
                        <div class="product-category"><?php echo getCategory($product['category_id'])['name']; ?></div>
                        <div class="product-name"><?php echo $product['name']; ?></div>
                        <div class="product-price">
                            <?php echo formatPrice($product['price']); ?>
                            <?php if ($product['original_price'] > $product['price']): ?>
                                <span class="product-price-original"><?php echo formatPrice($product['original_price']); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="product-rating">
                            <span class="stars">
                                <?php 
                                $fullStars = floor($product['rating']);
                                for ($i = 0; $i < $fullStars; $i++) echo '★';
                                for ($i = $fullStars; $i < 5; $i++) echo '☆';
                                ?>
                            </span>
                            <span>(<?php echo $product['reviews_count']; ?>)</span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php elseif ($query): ?>
        <div class="empty-cart">
            <div class="empty-icon">🔍</div>
            <h3>No products found</h3>
            <p>Try adjusting your search term</p>
            <a href="products.php" class="btn">Browse All Products</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
