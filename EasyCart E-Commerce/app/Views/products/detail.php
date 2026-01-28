<?php
// Variables passed from ProductController:
// $product, $page_title, $brand_recommendations, $category_recommendations, $other_recommendations
// Helper functions: $getCategory, $getBrand, $isInWishlist, $formatPrice
?>

<link rel="stylesheet" href="assets/css/product-details.css">

<!-- Breadcrumb -->
<div class="breadcrumb">
    <a href="index.php">Home</a> / 
    <a href="products.php?category=<?php echo $product['category_id']; ?>">
        <?php echo $getCategory($product['category_id'])['name']; ?>
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
            <div class="product-category-tag"><?php echo $getCategory($product['category_id'])['name']; ?></div>
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
                    <input type="number" class="quantity-input" id="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" onchange="validateMaxStock(this)" oninput="validateMaxStock(this)">
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
                <button class="btn btn-outline" onclick="saveForLater(<?php echo $product['id']; ?>, event)">
                    🔖 Save for Later
                </button>
            </div>

            <div class="product-meta">
                <div class="meta-item">
                    <strong>Brand:</strong> <?php echo $getBrand($product['brand_id'])['name']; ?>
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

        <?php include __DIR__ . '/../partials/reviews_section.php'; ?>
    </div>

    <!-- Recommendations Row 1: Different Brands, Same Category -->
    <?php 
    if (count($brand_recommendations) > 0) {
        $products = $brand_recommendations;
        $title = "Similar Products from Other Brands";
        $subtitle = "Compare options and find the perfect match";
        $showCategory = false;
        include __DIR__ . '/../partials/recommendation_section.php';
    }
    ?>

    <!-- Recommendations Row 2: Same Category -->
    <?php 
    if (count($category_recommendations) > 0) {
        $products = $category_recommendations;
        // Check if category exists before accessing name to avoid errors
        $cat = $getCategory($product['category_id']);
        $catName = $cat ? $cat['name'] : 'Same Category';
        $title = "More from " . $catName;
        $subtitle = "Explore similar products you might like";
        $showCategory = false;
        include __DIR__ . '/../partials/recommendation_section.php';
    }
    ?>

    <!-- Recommendations Row 3: Other Categories -->
    <?php 
    if (count($other_recommendations) > 0) {
        $products = $other_recommendations;
        $title = "You May Also Like";
        $subtitle = "Discover products from other categories";
        $showCategory = true;
        include __DIR__ . '/../partials/recommendation_section.php';
    }
    ?>
</div>

<!-- Page Specific Scripts -->
<script src="assets/js/product-detail.js"></script>

