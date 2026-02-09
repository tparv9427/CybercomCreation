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
                <div class="recommendation-card"
                    onclick="window.location.href='<?php echo \EasyCart\Helpers\ViewHelper::productUrl($rec); ?>'">
                    <div class="recommendation-image"><?php echo $rec['icon']; ?></div>
                    <div class="recommendation-info">
                        <?php if (isset($showCategory) && $showCategory): ?>
                            <div class="recommendation-category"><?php echo $getCategory($rec['category_id'])['name']; ?></div>
                        <?php else: ?>
                            <div class="recommendation-brand"><?php echo $getBrand($rec['brand_id'])['name']; ?></div>
                        <?php endif; ?>

                        <div class="recommendation-name"><?php echo $rec['name']; ?></div>
                        <div class="recommendation-price"><?php echo $formatPrice($rec['price']); ?></div>
                        <div class="recommendation-rating">
                            <span class="stars">
                                <?php
                                $fullStars = floor($rec['rating']);
                                for ($i = 0; $i < $fullStars; $i++)
                                    echo '★';
                                for ($i = $fullStars; $i < 5; $i++)
                                    echo '☆';
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>