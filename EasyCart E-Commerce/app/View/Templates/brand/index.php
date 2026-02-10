<?php
// This view is called from ProductController::brand
?>

<!-- Custom CSS for Products Page -->
<link rel="stylesheet" href="/assets/css/products.css">

<!-- Breadcrumb -->
<div class="breadcrumb">
    <a href="/">Home</a> /
    <a href="/products">Brands</a> /
    <?php echo $brand['name']; ?>
</div>

<!-- Main Content -->
<div class="container plp-container" style="grid-template-columns: 1fr;">
    <div class="plp-main">
        <div class="section-header">
            <h2 class="section-title"><?php echo $page_title ?? 'Brand'; ?></h2>
            <p class="section-subtitle">Browse all products from <?php echo $brand['name']; ?></p>
        </div>

        <!-- Filter Bar -->
        <div class="filter-bar" style="margin-bottom: 2rem;">
            <div class="results-count">Showing <?php echo $product_count; ?>
                product<?php echo $product_count != 1 ? 's' : ''; ?></div>
            <div class="view-toggle">
                <button class="view-btn active" onclick="toggleView('grid')">⊞</button>
                <button class="view-btn" onclick="toggleView('row')">☰</button>
            </div>
        </div>

        <!-- Product Grid View -->
        <div class="product-grid" id="gridView">
            <?php if (count($filtered_products) > 0): ?>
                <?php foreach ($filtered_products as $product): ?>
                    <?php include __DIR__ . '/../partials/product_card.php'; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="grid-column: 1/-1; text-align: center; padding: 4rem;">
                    <h3 style="font-size: 1.5rem; color: var(--secondary); margin-bottom: 1rem;">No products found for this
                        brand</h3>
                    <a href="/products" class="btn" style="margin-top: 2rem;">View All Products</a>
                </div>
            <?php endif; ?>
        </div>

        <?php include __DIR__ . '/../products/partials/product_row.php'; ?>
    </div>
</div>

<!-- Bottom Pagination -->
<?php
$style = '';
include __DIR__ . '/../partials/pagination.php';
?>
</div>