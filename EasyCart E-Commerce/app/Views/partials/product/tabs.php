<?php
/**
 * Product Information Tabs Partial
 * Includes Description, Specifications, and Reviews tabs
 */
?>
<!-- Product Information Tabs -->
<div class="product-info-section">
    <div class="tabs">
        <div class="tab active" onclick="switchTab(0)">Description</div>
        <div class="tab" onclick="switchTab(1)">Specifications</div>
        <div class="tab" onclick="switchTab(2)">Reviews</div>
    </div>

    <!-- Description Tab -->
    <?php include __DIR__ . '/product_description_tab.php'; ?>

    <!-- Specifications Tab -->
    <?php include __DIR__ . '/product_specifications_tab.php'; ?>

    <!-- Reviews Tab -->
    <?php include __DIR__ . '/reviews_section.php'; ?>
</div>
