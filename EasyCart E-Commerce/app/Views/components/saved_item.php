<?php
/** @var array $item */
if (!isset($item) || !isset($item['product'])) {
    return;
}

$productId = $item['product']['id'];
$productName = $item['product']['name'] ?? 'Unknown Product';
$productIcon = $item['product']['icon'] ?? '';
$price = $item['formatted_price'] ?? '$0.00';
?>
<div class="cart-item" style="opacity: 0.9;" id="saved-item-<?php echo $productId; ?>">
    <div class="item-image" onclick="window.location.href='/product/<?php echo $productId; ?>'">
        <?php echo $productIcon; ?>
    </div>
    <div class="item-details">
        <h3 class="item-name">
            <?php echo $productName; ?>
        </h3>
        <p class="item-price">
            <?php echo $price; ?>
        </p>
        <button class="btn btn-sm btn-outline" onclick="moveToCartFromSaved(<?php echo $productId; ?>)"
            style="margin-top: 0.5rem;">Move to Cart</button>
    </div>
    <div class="item-total"></div>
</div>