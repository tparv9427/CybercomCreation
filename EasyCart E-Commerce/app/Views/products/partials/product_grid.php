<!-- Product Grid View -->
<div class="product-grid" id="gridView">
    <?php if (count($filtered_products) > 0): ?>
        <?php foreach ($filtered_products as $product): ?>
            <?php include __DIR__ . '/../../partials/product_card.php'; ?>
        <?php endforeach; ?>
    <?php else: ?>
        <div style="grid-column: 1/-1; text-align: center; padding: 4rem;">
            <h3 style="font-size: 1.5rem; color: var(--secondary); margin-bottom: 1rem;">No products found</h3>
            <p style="color: var(--secondary);">Try adjusting your filters</p>
            <a href="/products" class="btn" style="margin-top: 2rem;">View All Products</a>
        </div>
    <?php endif; ?>
</div>

