<?php
/**
 * Recommendation Section Partial
 * 
 * Variables required:
 * $title (string)
 * $subtitle (string)
 * $products (array)
 * $getBrand (callable)
 * $getCategory (callable)
 * $formatPrice (callable)
 * $showCategory (bool, optional) - if true, shows category instead of brand
 */
?>
<?php if (count($products) > 0): ?>
    <div class="recommendations">
        <h3><?php echo $title; ?></h3>
        <p class="recommendations-subtitle"><?php echo $subtitle; ?></p>
        <div class="recommendation-grid">
            <?php foreach ($products as $rec): ?>
                <?php
                // Map $rec to $product for the partial
                $product = $rec;
                include __DIR__ . '/product_card.php';
                ?>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>