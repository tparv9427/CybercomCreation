<?php
// Variables passed from ProductController:
// $product, $page_title, $brand_recommendations, $category_recommendations, $other_recommendations
// Helper functions: $getCategory, $getBrand, $isInWishlist, $formatPrice
?>

<link rel="stylesheet" href="/assets/css/product-details.css">

<!-- Breadcrumb -->
<div class="breadcrumb">
    <a href="/">Home</a> /
    <?php if (isset($product['category_id']) && $product['category_id']): ?>
        <a href="/products?category=<?php echo $product['category_id']; ?>">
            <?php echo $getCategory($product['category_id'])['name']; ?>
        </a> /
    <?php endif; ?>
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

        <!-- Right Side - Details (Center Column) -->
        <div class="product-center">
            <h1 class="product-title"><?php echo $product['name']; ?></h1>
            <?php if (isset($product['brand_name']) && $product['brand_name']): ?>
                <a href="/brand/<?php echo urlencode($product['brand_name']); ?>" class="brand-link">
                    Visit the <?php echo htmlspecialchars($product['brand_name']); ?> Store
                </a>
            <?php endif; ?>

            <div class="product-rating-section">
                <div class="rating-row">
                    <span class="rating-score"><?php echo number_format($product['rating'], 1); ?></span>
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
                    <a href="#reviews" class="rating-link" onclick="switchTab(2)">
                        <?php echo number_format($product['reviews_count']); ?> ratings
                    </a>
                    <span style="color: #ccc;">|</span>
                    <a href="#" class="rating-link">Search this page</a>
                </div>

                <?php if (isset($product['bought_past_month'])): ?>
                    <div class="sales-signal-label">
                        <?php echo $product['bought_past_month']; ?> bought in past month
                    </div>
                <?php endif; ?>
            </div>

            <div class="price-section">
                <div class="price-main"><?php echo formatPrice($product['price']); ?></div>
                <?php if ($product['original_price'] > $product['price']): ?>
                    <div>
                        <span class="price-original"><?php echo formatPrice($product['original_price']); ?></span>
                        <span class="price-discount">Save <?php echo $product['discount_percent']; ?>% today!</span>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Trust Badges -->
            <?php include __DIR__ . '/../partials/trust_badges.php'; ?>

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

            <div class="product-meta">
                <?php if (isset($product['brand_name']) && $product['brand_name']): ?>
                    <div class="meta-item">
                        <strong>Brand:</strong> <?php echo htmlspecialchars($product['brand_name']); ?>
                    </div>
                <?php endif; ?>
                <div class="meta-item">
                    <strong>SKU:</strong> <?php echo strtoupper(substr($product['slug'], 0, 10)); ?>
                </div>
            </div>
        </div>

        <!-- Buy Box (Right Column) -->
        <div class="buy-box">
            <div class="buy-box-price"><?php echo formatPrice($product['price']); ?></div>

            <div class="shipping-type-display" style="margin-bottom: 1rem; font-size: 0.9rem; color: #565959;">
                <?php if ($product['price'] >= 300): ?>
                    <span style="color: #B12704; font-weight: bold;">Freight Shipping</span>
                    <br> Heavy item delivery
                <?php else: ?>
                    <span style="color: #007600; font-weight: bold;">Express Shipping</span>
                    <br> Fast delivery available
                <?php endif; ?>
            </div>





            <?php if ($product['stock'] > 10): ?>
                <div class="stock-status in-stock">
                    In Stock
                    <span style="font-size: 0.9rem; font-weight: 400; color: #565959;">(<?php echo $product['stock']; ?>
                        available)</span>
                </div>
            <?php elseif ($product['stock'] > 0): ?>
                <div class="stock-status low-stock" style="color: #c7511f;">
                    Only <?php echo $product['stock']; ?> left in stock - order soon.
                </div>
            <?php else: ?>
                <div class="stock-status out-of-stock">Out of Stock</div>
            <?php endif; ?>

            <div class="quantity-wrapper">
                <label for="qty-select">Quantity:</label>
                <select id="qty-select" class="qty-dropdown">
                    <?php for ($i = 1; $i <= min(10, max(1, $product['stock'])); $i++): ?>
                        <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="buy-box-buttons">
                <button class="btn-buy-box btn-add-to-cart add-to-cart-btn-<?php echo $product['id']; ?>"
                    data-product-id="<?php echo $product['id']; ?>"
                    onclick="addToCart(<?php echo $product['id']; ?>, event)">
                    Add to Cart
                </button>
                <button class="btn-buy-box btn-buy-now" onclick="buyNow(<?php echo $product['id']; ?>)">
                    Buy Now
                </button>
            </div>

            <div class="seller-info">
                <div class="seller-row">
                    <span>Ships from</span> <span>EasyCart</span>
                </div>
                <?php if (isset($product['brand_name']) && $product['brand_name']): ?>
                    <div class="seller-row">
                        <span>Sold by</span> <span><?php echo htmlspecialchars($product['brand_name']); ?> Retail</span>
                    </div>
                <?php endif; ?>
            </div>

            <div class="wishlist-row">
                <button class="btn-text-link <?php echo isInWishlist($product['id']) ? 'active' : ''; ?>"
                    onclick="toggleWishlist(<?php echo $product['id']; ?>, event)">
                    <?php echo isInWishlist($product['id']) ? 'Remove from Wishlist' : 'Add to Wishlist'; ?>
                </button>
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
<script src="/assets/js/product-detail.js"></script>