<?php
// This view is called from ProductController
// Variables available: $filtered_products, $page_title, $product_count, $categories, $category_id, $brand_id, $price_range, $rating_filter
?>


<!-- Breadcrumb -->
<div class="breadcrumb">
    <a href="index.php">Home</a> / 
    <?php if ($category_id): ?>
        <?php echo getCategory($category_id)['name']; ?>
    <?php else: ?>
        All Products
    <?php endif; ?>
</div>

<!-- Main Content -->
<div class="container">
    <div class="section-header">
        <h2 class="section-title"><?php echo $page_title; ?></h2>
        <p class="section-subtitle">Discover amazing products</p>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <div class="filter-container">
            <div class="filter-group">
                <div class="filter-label">Category</div>
                <div class="filter-options">
                    <a href="products.php">
                        <button class="filter-btn <?php echo !$category_id ? 'active' : ''; ?>">All Products</button>
                    </a>
                    <?php foreach ($categories as $cat): ?>
                        <a href="products.php?category=<?php echo $cat['id']; ?>">
                            <button class="filter-btn <?php echo $category_id == $cat['id'] ? 'active' : ''; ?>">
                                <?php echo $cat['name']; ?>
                            </button>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="filter-group">
                <div class="filter-label">Price Range</div>
                <div class="filter-options">
                    <a href="?<?php echo $category_id ? 'category='.$category_id : ''; ?>">
                        <button class="filter-btn <?php echo !$price_range ? 'active' : ''; ?>">Any Price</button>
                    </a>
                    <a href="?<?php echo $category_id ? 'category='.$category_id.'&' : ''; ?>price=under50">
                        <button class="filter-btn <?php echo $price_range == 'under50' ? 'active' : ''; ?>">Under $50</button>
                    </a>
                    <a href="?<?php echo $category_id ? 'category='.$category_id.'&' : ''; ?>price=50-100">
                        <button class="filter-btn <?php echo $price_range == '50-100' ? 'active' : ''; ?>">$50 - $100</button>
                    </a>
                    <a href="?<?php echo $category_id ? 'category='.$category_id.'&' : ''; ?>price=100-200">
                        <button class="filter-btn <?php echo $price_range == '100-200' ? 'active' : ''; ?>">$100 - $200</button>
                    </a>
                    <a href="?<?php echo $category_id ? 'category='.$category_id.'&' : ''; ?>price=200plus">
                        <button class="filter-btn <?php echo $price_range == '200plus' ? 'active' : ''; ?>">$200+</button>
                    </a>
                </div>
            </div>

            <div class="filter-group">
                <div class="filter-label">Rating</div>
                <div class="filter-options">
                    <a href="?<?php echo $category_id ? 'category='.$category_id : ''; ?>">
                        <button class="filter-btn <?php echo !$rating_filter ? 'active' : ''; ?>">All Ratings</button>
                    </a>
                    <a href="?<?php echo $category_id ? 'category='.$category_id.'&' : ''; ?>rating=5">
                        <button class="filter-btn <?php echo $rating_filter == 5 ? 'active' : ''; ?>">5 Stars</button>
                    </a>
                    <a href="?<?php echo $category_id ? 'category='.$category_id.'&' : ''; ?>rating=4">
                        <button class="filter-btn <?php echo $rating_filter == 4 ? 'active' : ''; ?>">4+ Stars</button>
                    </a>
                    <a href="?<?php echo $category_id ? 'category='.$category_id.'&' : ''; ?>rating=3">
                        <button class="filter-btn <?php echo $rating_filter == 3 ? 'active' : ''; ?>">3+ Stars</button>
                    </a>
                </div>
            </div>
        </div>

        <div class="view-controls">
            <div class="results-count">Showing <?php echo $product_count; ?> product<?php echo $product_count != 1 ? 's' : ''; ?></div>
            <div class="view-toggle">
                <button class="view-btn active" onclick="toggleView('grid')">⊞</button>
                <button class="view-btn" onclick="toggleView('row')">☰</button>
            </div>
        </div>
    </div>

    <!-- Product Grid View -->
    <div class="product-grid" id="gridView">
        <?php if (count($filtered_products) > 0): ?>
            <?php foreach ($filtered_products as $product): ?>
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
                                $halfStar = ($product['rating'] - $fullStars) >= 0.5;
                                for ($i = 0; $i < $fullStars; $i++) echo '★';
                                if ($halfStar) echo '☆';
                                for ($i = ceil($product['rating']); $i < 5; $i++) echo '☆';
                                ?>
                            </span>
                            <span>(<?php echo $product['reviews_count']; ?>)</span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 4rem;">
                <h3 style="font-size: 1.5rem; color: var(--secondary); margin-bottom: 1rem;">No products found</h3>
                <p style="color: var(--secondary);">Try adjusting your filters</p>
                <a href="products.php" class="btn" style="margin-top: 2rem;">View All Products</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Product Row View -->
    <div class="product-row" id="rowView">
        <?php if (count($filtered_products) > 0): ?>
            <?php foreach ($filtered_products as $product): ?>
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
                        <div class="product-category"><?php echo getCategory($product['category_id'])['name']; ?></div>
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
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>
