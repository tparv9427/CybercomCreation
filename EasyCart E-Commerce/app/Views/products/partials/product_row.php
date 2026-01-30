<!-- Product Row View -->
<div class="product-row" id="rowView">
    <?php if (count($filtered_products) > 0): ?>
        <?php foreach ($filtered_products as $product): ?>
            <?php include __DIR__ . '/../../partials/product_row_item.php'; ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>