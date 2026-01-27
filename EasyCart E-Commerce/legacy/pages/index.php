<?php
require_once 'includes/config.php';
$page_title = 'Home';
include 'includes/header.php';
?>

<!-- Banner Carousel -->
<div class="banner-carousel">
    <div class="banner-slide">
        <div class="banner-content">
            <h1>Elegant Living, Redefined</h1>
            <p>Discover our curated collection of premium products</p>
            <a href="products.php" class="btn">Explore Collection</a>
        </div>
    </div>
    <div class="banner-slide">
        <div class="banner-content">
            <h1>Top Brands Collection</h1>
            <p>Explore exclusive products from leading brands</p>
            <a href="brand.php?id=1" class="btn">Shop TechPro</a>
        </div>
    </div>
    <div class="banner-slide">
        <div class="banner-content">
            <h1>Exclusive Member Benefits</h1>
            <p>Join today and enjoy special privileges</p>
            <a href="signup.php" class="btn">Learn More</a>
        </div>
    </div>
</div>

<!-- Featured Products -->
<div class="container">
    <div class="section-header">
        <h2 class="section-title">Featured Products</h2>
        <p class="section-subtitle">Handpicked items just for you</p>
    </div>

    <div class="product-grid">
        <?php 
        $featured = getFeaturedProducts();
        foreach ($featured as $product): 
        ?>
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
    </div>
</div>

<!-- Categories Section -->
<div class="container">
    <div class="section-header">
        <h2 class="section-title">Shop by Category</h2>
        <p class="section-subtitle">Find what you're looking for</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
        <?php foreach ($categories as $category): ?>
            <a href="products.php?category=<?php echo $category['id']; ?>" style="text-decoration: none;">
                <div style="background: var(--card-bg); padding: 3rem 2rem; border-radius: 20px; text-align: center; transition: all 0.3s; box-shadow: var(--shadow); cursor: pointer;">
                    <h3 style="font-family: 'Cormorant Garamond', serif; font-size: 1.8rem; color: var(--primary); margin-bottom: 0.5rem;"><?php echo $category['name']; ?></h3>
                    <p style="color: var(--secondary);">Explore →</p>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- New Arrivals -->
<?php 
$newProducts = getNewProducts();
if (count($newProducts) > 0): 
?>
<div class="container">
    <div class="section-header">
        <h2 class="section-title">New Arrivals</h2>
        <p class="section-subtitle">Check out our latest additions</p>
    </div>

    <div class="product-grid">
        <?php foreach ($newProducts as $product): ?>
            <div class="product-card" onclick="window.location.href='product.php?id=<?php echo $product['id']; ?>'">
                <div class="product-image">
                    <?php echo $product['icon']; ?>
                    <span class="product-badge new">New</span>
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
    </div>
</div>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
