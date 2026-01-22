<?php
require_once 'includes/config.php';

$brand_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;
$brand = getBrand($brand_id);

if (!$brand) {
    header('Location: index.php');
    exit;
}

$page_title = $brand['name'] . ' - Brands';
$brand_products = getProductsByBrand($brand_id);

// Get top products (highest rated)
$top_products = $brand_products;
usort($top_products, function($a, $b) {
    return $b['rating'] <=> $a['rating'];
});
$top_products = array_slice($top_products, 0, 5);

// Get best sellers (most reviews)
$best_sellers = $brand_products;
usort($best_sellers, function($a, $b) {
    return $b['reviews_count'] <=> $a['reviews_count'];
});
$best_sellers = array_slice($best_sellers, 0, 6);

// Get products with biggest discounts
$offers = array_filter($brand_products, function($p) {
    return $p['discount_percent'] > 20;
});
$offers = array_slice($offers, 0, 4);

include 'includes/header.php';
?>

<style>
.brand-banner {
    height: 400px;
    background: var(--gradient-1);
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

.brand-banner-slide {
    position: absolute;
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    animation: brandSlide 15s infinite;
}

.brand-banner-slide:nth-child(2) {
    animation-delay: 5s;
    opacity: 0;
}

.brand-banner-slide:nth-child(3) {
    animation-delay: 10s;
    opacity: 0;
}

@keyframes brandSlide {
    0% { opacity: 0; transform: scale(1.05); }
    6% { opacity: 1; transform: scale(1); }
    33% { opacity: 1; transform: scale(1); }
    40% { opacity: 0; transform: scale(0.95); }
    100% { opacity: 0; }
}

.brand-content {
    text-align: center;
    color: white;
    z-index: 10;
}

.brand-content h1 {
    font-family: 'Cormorant Garamond', serif;
    font-size: 4rem;
    margin-bottom: 1rem;
}

.offer-banner {
    background: linear-gradient(135deg, var(--accent) 0%, #E55A2B 100%);
    padding: 3rem 2rem;
    border-radius: 20px;
    text-align: center;
    color: white;
    position: relative;
    overflow: hidden;
}

.offer-banner::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 300px;
    height: 300px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
}

.offer-title {
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
}

.offer-subtitle {
    font-size: 1.2rem;
    margin-bottom: 0.5rem;
}

.offer-terms {
    font-size: 0.7rem;
    opacity: 0.7;
    margin-top: 1rem;
}
</style>

<div class="breadcrumb">
    <a href="index.php">Home</a> / Brands / <?php echo $brand['name']; ?>
</div>

<!-- Brand Banner -->
<div class="brand-banner">
    <?php foreach ($top_products as $index => $product): ?>
        <div class="brand-banner-slide">
            <div style="font-size: 12rem; opacity: 0.2; position: absolute;"><?php echo $product['icon']; ?></div>
            <div class="brand-content">
                <h1><?php echo $brand['name']; ?></h1>
                <p style="font-size: 1.5rem; margin-bottom: 2rem;">Top Rated: <?php echo $product['name']; ?></p>
                <a href="product.php?id=<?php echo $product['id']; ?>" class="btn">View Product</a>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Best Sellers -->
<div class="container">
    <div class="section-header">
        <h2 class="section-title">Best Sellers</h2>
        <p class="section-subtitle">Most popular products from <?php echo $brand['name']; ?></p>
    </div>

    <div class="product-grid">
        <?php foreach ($best_sellers as $product): ?>
            <div class="product-card" onclick="window.location.href='product.php?id=<?php echo $product['id']; ?>'">
                <div class="product-image">
                    <?php echo $product['icon']; ?>
                    <button class="wishlist-btn <?php echo isInWishlist($product['id']) ? 'active' : ''; ?>" 
                            onclick="toggleWishlist(<?php echo $product['id']; ?>, event)">
                        <?php echo isInWishlist($product['id']) ? '❤️' : '🤍'; ?>
                    </button>
                </div>
                <div class="product-info">
                    <div class="product-category"><?php echo getCategory($product['category_id'])['name']; ?></div>
                    <div class="product-name"><?php echo $product['name']; ?></div>
                    <div class="product-price"><?php echo formatPrice($product['price']); ?></div>
                    <div class="product-rating">
                        <span class="stars">★★★★★</span>
                        <span>(<?php echo $product['reviews_count']; ?>)</span>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Special Offers -->
<?php if (count($offers) > 0): ?>
<div class="container">
    <div class="offer-banner">
        <h2 class="offer-title">🔥 SPECIAL OFFERS 🔥</h2>
        <p class="offer-subtitle">Save big on these amazing products!</p>
        <p class="offer-subtitle">Up to 40% OFF - Limited Time Only</p>
        <p class="offer-terms">*T&C Apply. Offer valid while stocks last. Cannot be combined with other offers.</p>
    </div>

    <div style="margin-top: 2rem;">
        <div class="product-grid">
            <?php foreach ($offers as $product): ?>
                <div class="product-card" onclick="window.location.href='product.php?id=<?php echo $product['id']; ?>'">
                    <div class="product-image">
                        <?php echo $product['icon']; ?>
                        <span class="product-badge">-<?php echo $product['discount_percent']; ?>%</span>
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
                            <span class="product-price-original"><?php echo formatPrice($product['original_price']); ?></span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- All Products -->
<div class="container">
    <div class="section-header">
        <h2 class="section-title">All <?php echo $brand['name']; ?> Products</h2>
        <p class="section-subtitle"><?php echo count($brand_products); ?> products available</p>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <div class="filter-container">
            <div class="filter-group">
                <div class="filter-label">Category</div>
                <div class="filter-options">
                    <button class="filter-btn active">All</button>
                    <?php
                    $brand_categories = [];
                    foreach ($brand_products as $p) {
                        $cat_id = $p['category_id'];
                        if (!in_array($cat_id, $brand_categories)) {
                            $brand_categories[] = $cat_id;
                            echo '<button class="filter-btn">' . getCategory($cat_id)['name'] . '</button>';
                        }
                    }
                    ?>
                </div>
            </div>

            <div class="filter-group">
                <div class="filter-label">Price</div>
                <div class="filter-options">
                    <button class="filter-btn active">Any Price</button>
                    <button class="filter-btn">Under $50</button>
                    <button class="filter-btn">$50-$100</button>
                    <button class="filter-btn">$100+</button>
                </div>
            </div>
        </div>

        <div class="view-controls">
            <div class="results-count">Showing <?php echo count($brand_products); ?> products</div>
            <div class="view-toggle">
                <button class="view-btn active" onclick="toggleView('grid')">⊞</button>
                <button class="view-btn" onclick="toggleView('row')">☰</button>
            </div>
        </div>
    </div>

    <div class="product-grid" id="gridView">
        <?php foreach ($brand_products as $product): ?>
            <div class="product-card" onclick="window.location.href='product.php?id=<?php echo $product['id']; ?>'">
                <div class="product-image">
                    <?php echo $product['icon']; ?>
                    <?php if ($product['discount_percent'] > 0): ?>
                        <span class="product-badge">-<?php echo $product['discount_percent']; ?>%</span>
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

    <div class="product-row" id="rowView"></div>
</div>

<?php include 'includes/footer.php'; ?>
