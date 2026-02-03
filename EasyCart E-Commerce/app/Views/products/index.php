<?php
// This view is called from ProductController
// Variables available: $filtered_products, $page_title, $product_count, $categories, $category_id, $brand_id, $price_range, $rating_filter
?>

<?php include __DIR__ . '/partials/breadcrumb.php'; ?>

<!-- Custom CSS for Products Page -->
<link rel="stylesheet" href="/assets/css/products.css">

<!-- Main Content -->
<div class="container plp-container">
    <!-- Sidebar Filters -->
    <aside class="plp-sidebar">
        <?php include __DIR__ . '/partials/sidebar.php'; ?>
    </aside>

    <!-- Main Content Grid -->
    <div class="plp-main">
        <div class="section-header">
            <h2 class="section-title"><?php echo $page_title; ?></h2>
            <p class="section-subtitle">Discover amazing products</p>
        </div>

        <?php include __DIR__ . '/partials/controls.php'; ?>

        <!-- Top Pagination -->
        <?php
        $style = 'margin-bottom: 2rem;';
        include __DIR__ . '/../partials/pagination.php';
        ?>

        <?php include __DIR__ . '/partials/product_grid.php'; ?>

        <?php include __DIR__ . '/partials/product_row.php'; ?>

        <!-- Pagination -->
        <?php
        $style = '';
        include __DIR__ . '/../partials/pagination.php';
        ?>
    </div>
</div>

