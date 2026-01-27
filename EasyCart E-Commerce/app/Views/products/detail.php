<?php
require_once 'includes/config.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$product = getProduct($product_id);

if (!$product) {
    header('Location: products.php');
    exit;
}

$page_title = $product['name'];

// Get recommendations
// Row 1: Same product from different brands with different prices
$brand_recommendations = array_filter($products, function($p) use ($product) {
    return $p['id'] != $product['id'] && 
           $p['category_id'] == $product['category_id'] && 
           $p['brand_id'] != $product['brand_id'];
});
$brand_recommendations = array_slice($brand_recommendations, 0, 4);

// Row 2: Same category products
$category_recommendations = array_filter($products, function($p) use ($product) {
    return $p['id'] != $product['id'] && 
           $p['category_id'] == $product['category_id'];
});
$category_recommendations = array_slice($category_recommendations, 0, 4);

// Row 3: Other categories
$other_recommendations = array_filter($products, function($p) use ($product) {
    return $p['id'] != $product['id'] && 
           $p['category_id'] != $product['category_id'];
});
$other_recommendations = array_slice($other_recommendations, 0, 4);

include 'includes/header.php';
?>

<link rel="stylesheet" href="assets/css/product-details.css">

<!-- Breadcrumb -->
<div class="breadcrumb">
    <a href="index.php">Home</a> / 
    <a href="products.php?category=<?php echo $product['category_id']; ?>">
        <?php echo getCategory($product['category_id'])['name']; ?>
    </a> / 
    <?php echo $product['name']; ?>
</div>

<!-- Product Container -->
<div class="product-container">
    <!-- Main Product Section -->
    <div class="product-main">
        <!-- Left Side - Images -->
        <div class="product-images">
            <div class="main-image"><?php echo $product['icon']; ?></div>

        </div>

        <!-- Right Side - Details -->
        <div class="product-details">
            <div class="product-category-tag"><?php echo getCategory($product['category_id'])['name']; ?></div>
            <h1 class="product-title"><?php echo $product['name']; ?></h1>
            
            <div class="product-rating-section">
                <span class="stars">
                    <?php 
                    $fullStars = floor($product['rating']);
                    $halfStar = ($product['rating'] - $fullStars) >= 0.5;
                    for ($i = 0; $i < $fullStars; $i++) echo '★';
                    if ($halfStar) echo '☆';
                    for ($i = ceil($product['rating']); $i < 5; $i++) echo '☆';
                    ?>
                </span>
                <span class="rating-text"><?php echo $product['rating']; ?> (<?php echo $product['reviews_count']; ?> reviews)</span>
            </div>

            <div class="price-section">
                <div class="price-main"><?php echo formatPrice($product['price']); ?></div>
                <?php if ($product['original_price'] > $product['price']): ?>
                    <div>
                        <span class="price-original"><?php echo formatPrice($product['original_price']); ?></span>
                        <span class="price-discount">Save <?php echo $product['discount_percent']; ?>% today!</span>
                    </div>
                <?php endif; ?>
                <div class="stock-info">
                    <?php if ($product['stock'] > 10): ?>
                        <span class="in-stock">✓ In Stock (<?php echo $product['stock']; ?> available)</span>
                    <?php elseif ($product['stock'] > 0): ?>
                        <span class="low-stock">⚠ Only <?php echo $product['stock']; ?> left!</span>
                    <?php else: ?>
                        <span class="out-of-stock">✗ Out of Stock</span>
                    <?php endif; ?>
                </div>
            </div>

            <div class="offers">
                <h4>Available Offers</h4>
                <ul>
                    <li>Bank Offer: 10% off on HDFC Bank Cards</li>
                    <li>Free Shipping on orders above $50</li>
                    <li>Exchange Offer: Get up to $30 off on exchange</li>
                    <li>No Cost EMI available on select cards</li>
                </ul>
            </div>

            <?php if (isset($product['variants'])): ?>
                <?php foreach ($product['variants'] as $variant_name => $variant_options): ?>
                    <div class="variant-section">
                        <div class="variant-label">Select <?php echo ucfirst($variant_name); ?>:</div>
                        <div class="variant-options">
                            <?php foreach ($variant_options as $index => $option): ?>
                                <button class="variant-btn <?php echo $index === 0 ? 'active' : ''; ?>">
                                    <?php echo $option; ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

            <div class="quantity-section">
                <div class="quantity-label">Quantity:</div>
                <div class="quantity-controls">
                    <button class="quantity-btn" onclick="decreaseQty()">−</button>
                    <input type="number" class="quantity-input" id="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>">
                    <button class="quantity-btn" onclick="increaseQty()">+</button>
                </div>
            </div>

            <div class="action-buttons">
                <button class="btn btn-primary" onclick="addToCart(<?php echo $product['id']; ?>, event)">
                    Add to Cart
                </button>
                <button class="btn btn-wishlist <?php echo isInWishlist($product['id']) ? 'active' : ''; ?>" 
                        onclick="toggleWishlist(<?php echo $product['id']; ?>, event)">
                    <?php echo isInWishlist($product['id']) ? '❤️ In Wishlist' : '🤍 Add to Wishlist'; ?>
                </button>
            </div>

            <div class="product-meta">
                <div class="meta-item">
                    <strong>Brand:</strong> <?php echo getBrand($product['brand_id'])['name']; ?>
                </div>
                <div class="meta-item">
                    <strong>SKU:</strong> <?php echo strtoupper(substr($product['slug'], 0, 10)); ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Information Tabs -->
    <div class="product-info-section">
        <div class="tabs">
            <div class="tab active" onclick="switchTab(0)">Description</div>
            <div class="tab" onclick="switchTab(1)">Specifications</div>
            <div class="tab" onclick="switchTab(2)">Reviews</div>
        </div>

        <div class="tab-content active">
            <div class="description">
                <h3 style="margin-bottom: 1rem;">Product Description</h3>
                <p style="margin-bottom: 1rem;"><?php echo $product['long_description']; ?></p>
                <h4 style="margin: 2rem 0 1rem;">Key Features</h4>
                <ul class="features-list">
                    <?php foreach ($product['features'] as $feature): ?>
                        <li><?php echo $feature; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>

        <div class="tab-content">
            <h3 style="margin-bottom: 1rem;">Technical Specifications</h3>
            <ul class="features-list">
                <?php foreach ($product['specifications'] as $key => $value): ?>
                    <li><strong><?php echo $key; ?>:</strong> <?php echo $value; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="tab-content">
            <h3 style="margin-bottom: 2rem;">Customer Reviews</h3>
            
            <div class="review-item">
                <div class="review-header">
                    <div>
                        <div class="reviewer-name">John D.</div>
                        <div class="stars">★★★★★</div>
                    </div>
                    <div class="review-date">January 15, 2026</div>
                </div>
                <p>Absolutely fantastic product! The build quality is exceptional and the performance exceeds my expectations. Highly recommended!</p>
            </div>

            <div class="review-item">
                <div class="review-header">
                    <div>
                        <div class="reviewer-name">Sarah M.</div>
                        <div class="stars">★★★★★</div>
                    </div>
                    <div class="review-date">January 12, 2026</div>
                </div>
                <p>Best purchase I've made this year! The quality is outstanding and it works perfectly. Will definitely buy again.</p>
            </div>

            <div class="review-item">
                <div class="review-header">
                    <div>
                        <div class="reviewer-name">Mike R.</div>
                        <div class="stars">★★★★☆</div>
                    </div>
                    <div class="review-date">January 8, 2026</div>
                </div>
                <p>Great product overall. Minor issues but customer service was helpful. Would recommend with small reservations.</p>
            </div>
        </div>
    </div>

    <!-- Recommendations Row 1: Different Brands, Same Category -->
    <?php if (count($brand_recommendations) > 0): ?>
    <div class="recommendations">
        <h3>Similar Products from Other Brands</h3>
        <p class="recommendations-subtitle">Compare options and find the perfect match</p>
        <div class="recommendation-grid">
            <?php foreach ($brand_recommendations as $rec): ?>
                <div class="recommendation-card" onclick="window.location.href='product.php?id=<?php echo $rec['id']; ?>'">
                    <div class="recommendation-image"><?php echo $rec['icon']; ?></div>
                    <div class="recommendation-info">
                        <div class="recommendation-brand"><?php echo getBrand($rec['brand_id'])['name']; ?></div>
                        <div class="recommendation-name"><?php echo $rec['name']; ?></div>
                        <div class="recommendation-price"><?php echo formatPrice($rec['price']); ?></div>
                        <div class="recommendation-rating">
                            <span class="stars">
                                <?php 
                                $fullStars = floor($rec['rating']);
                                for ($i = 0; $i < $fullStars; $i++) echo '★';
                                for ($i = $fullStars; $i < 5; $i++) echo '☆';
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Recommendations Row 2: Same Category -->
    <?php if (count($category_recommendations) > 0): ?>
    <div class="recommendations">
        <h3>More from <?php echo getCategory($product['category_id'])['name']; ?></h3>
        <p class="recommendations-subtitle">Explore similar products you might like</p>
        <div class="recommendation-grid">
            <?php foreach ($category_recommendations as $rec): ?>
                <div class="recommendation-card" onclick="window.location.href='product.php?id=<?php echo $rec['id']; ?>'">
                    <div class="recommendation-image"><?php echo $rec['icon']; ?></div>
                    <div class="recommendation-info">
                        <div class="recommendation-brand"><?php echo getBrand($rec['brand_id'])['name']; ?></div>
                        <div class="recommendation-name"><?php echo $rec['name']; ?></div>
                        <div class="recommendation-price"><?php echo formatPrice($rec['price']); ?></div>
                        <div class="recommendation-rating">
                            <span class="stars">
                                <?php 
                                $fullStars = floor($rec['rating']);
                                for ($i = 0; $i < $fullStars; $i++) echo '★';
                                for ($i = $fullStars; $i < 5; $i++) echo '☆';
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Recommendations Row 3: Other Categories -->
    <?php if (count($other_recommendations) > 0): ?>
    <div class="recommendations">
        <h3>You May Also Like</h3>
        <p class="recommendations-subtitle">Discover products from other categories</p>
        <div class="recommendation-grid">
            <?php foreach ($other_recommendations as $rec): ?>
                <div class="recommendation-card" onclick="window.location.href='product.php?id=<?php echo $rec['id']; ?>'">
                    <div class="recommendation-image"><?php echo $rec['icon']; ?></div>
                    <div class="recommendation-info">
                        <div class="recommendation-category"><?php echo getCategory($rec['category_id'])['name']; ?></div>
                        <div class="recommendation-name"><?php echo $rec['name']; ?></div>
                        <div class="recommendation-price"><?php echo formatPrice($rec['price']); ?></div>
                        <div class="recommendation-rating">
                            <span class="stars">
                                <?php 
                                $fullStars = floor($rec['rating']);
                                for ($i = 0; $i < $fullStars; $i++) echo '★';
                                for ($i = $fullStars; $i < 5; $i++) echo '☆';
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function switchTab(index) {
    const tabs = document.querySelectorAll('.tab');
    const contents = document.querySelectorAll('.tab-content');
    
    tabs.forEach(t => t.classList.remove('active'));
    contents.forEach(c => c.classList.remove('active'));
    
    tabs[index].classList.add('active');
    contents[index].classList.add('active');
}

function increaseQty() {
    const input = document.getElementById('quantity');
    const max = parseInt(input.max);
    if (parseInt(input.value) < max) {
        input.value = parseInt(input.value) + 1;
    }
}

function decreaseQty() {
    const input = document.getElementById('quantity');
    if (parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
    }
}

// Variant selection
document.querySelectorAll('.variant-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        this.parentElement.querySelectorAll('.variant-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
    });
});

// Thumbnail switching
const thumbnails = document.querySelectorAll('.thumbnail');
thumbnails.forEach(thumb => {
    thumb.addEventListener('click', () => {
        thumbnails.forEach(t => t.classList.remove('active'));
        thumb.classList.add('active');
    });
});
</script>

<?php include 'includes/footer.php'; ?>
