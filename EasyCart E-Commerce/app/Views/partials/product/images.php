<?php
/**
 * Product Images Partial
 */
?>
<div class="product-images">
    <div class="main-image"><?php echo $product['icon']; ?></div>
    
    <!-- Thumbnails placeholder if needed in future -->
    <?php if (isset($product['images']) && is_array($product['images']) && count($product['images']) > 1): ?>
        <div class="thumbnails">
            <?php foreach ($product['images'] as $index => $img): ?>
                <div class="thumbnail <?php echo $index === 0 ? 'active' : ''; ?>">
                    <?php echo $img; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
