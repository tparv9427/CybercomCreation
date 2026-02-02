<?php
// This view is called from HomeController
// Variables available: $featured, $newProducts, $categories, $page_title
?>


<?php
// Load banner data from config
$banners = require APP_ROOT . '/config/banners.php';

// Include banner carousel component
include __DIR__ . '/../partials/banner_carousel.php';
?>

<!-- Featured Products -->
<div class="container">
    <div class="section-header">
        <h2 class="section-title">Featured Products</h2>
        <p class="section-subtitle">Handpicked items just for you</p>
    </div>

    <div class="product-grid">
        <?php foreach ($featured as $product): ?>
            <?php include __DIR__ . '/../partials/product_card.php'; ?>
        <?php endforeach; ?>
    </div>
</div>

<?php
// Category grid uses default title and subtitle
include __DIR__ . '/../partials/category_grid.php';
?>

<!-- New Arrivals -->
<?php
// $newProducts is passed from HomeController
if (count($newProducts) > 0):
    ?>
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">New Arrivals</h2>
            <p class="section-subtitle">Check out our latest additions</p>
        </div>

        <div class="product-grid">
            <?php foreach ($newProducts as $product): ?>
                <?php include __DIR__ . '/../partials/product_card.php'; ?>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>