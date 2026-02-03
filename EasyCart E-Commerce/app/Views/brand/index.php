<?php
// This view is called from ProductController::brand
?>

<!-- Breadcrumb -->
<div class="breadcrumb">
    <a href="/">Home</a> / 
    <a href="/products">Brands</a> / 
    <?php echo $brand['name']; ?>
</div>

<!-- Main Content -->
<div class="container">
    <div class="section-header">
        <h2 class="section-title"><?php echo $page_title; ?></h2>
        <p class="section-subtitle">Browse all products from <?php echo $brand['name']; ?></p>
    </div>

    <!-- Filter Bar (Reduced for Brand Page) -->
    <div class="filter-bar">
        <div class="filter-container">
            <div class="filter-group">
                <div class="filter-label">Categories</div>
                <div class="filter-options">
                    <button class="filter-btn active">All <?php echo $brand['name']; ?></button>
                    <!-- In a real app, we would filter categories available for this brand -->
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

    <!-- Top Pagination -->
    <?php 
    $style = 'margin-bottom: 2rem;';
    include __DIR__ . '/../partials/pagination.php'; 
    ?>

    <!-- Product Grid View -->
    <div class="product-grid" id="gridView">
        <?php if (count($filtered_products) > 0): ?>
            <?php foreach ($filtered_products as $product): ?>
                <?php include __DIR__ . '/../partials/product_card.php'; ?>
            <?php endforeach; ?>
        <?php else: ?>
            <div style="grid-column: 1/-1; text-align: center; padding: 4rem;">
                <h3 style="font-size: 1.5rem; color: var(--secondary); margin-bottom: 1rem;">No products found for this brand</h3>
                <a href="/products" class="btn" style="margin-top: 2rem;">View All Products</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Product Row View -->
    <div class="product-row" id="rowView">
        <?php if (count($filtered_products) > 0): ?>
            <?php foreach ($filtered_products as $product): ?>
                <?php include __DIR__ . '/../partials/product_row_item.php'; ?>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <!-- Bottom Pagination -->
    <?php 
    $style = '';
    include __DIR__ . '/../partials/pagination.php'; 
    ?>
</div>


