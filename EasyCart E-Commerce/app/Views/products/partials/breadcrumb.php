<div class="breadcrumb">
    <a href="/">Home</a> /
    <?php if ($category_id): ?>
        <?php echo $getCategory($category_id)['name']; ?>
    <?php else: ?>
        All Products
    <?php endif; ?>
</div>

