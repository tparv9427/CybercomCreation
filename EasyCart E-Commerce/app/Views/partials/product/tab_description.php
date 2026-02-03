<?php
/**
 * Product Description Tab Content
 */
?>
<div class="tab-content active">
    <div class="description">
        <h3 style="margin-bottom: 1rem;">Product Description</h3>
        <p style="margin-bottom: 1rem;"><?php echo $product['long_description']; ?></p>
        <h4 style="margin: 2rem 0 1rem;">Key Features</h4>
        <ul class="features-list">
            <?php foreach ($product['features'] as $feature): ?>
                <li><?php echo $feature; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>


