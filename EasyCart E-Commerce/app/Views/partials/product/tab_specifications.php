<?php
/**
 * Product Specifications Tab Content
 */
?>
<div class="tab-content">
    <h3 style="margin-bottom: 1rem;">Technical Specifications</h3>
    <ul class="features-list">
        <?php foreach ($product['specifications'] as $key => $value): ?>
            <li><strong><?php echo $key; ?>:</strong> <?php echo $value; ?></li>
        <?php endforeach; ?>
    </ul>
</div>


