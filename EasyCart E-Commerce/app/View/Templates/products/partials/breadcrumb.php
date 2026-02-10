<div class="breadcrumb">
    <a href="/">Home</a> /
    <?php if ($category_id): ?>
        <?php
        $breadcrumbCat = getCategory($category_id);
        echo $breadcrumbCat['name'] ?? 'Category';
        ?>
    <?php else: ?>
        All Products
    <?php endif; ?>
</div>