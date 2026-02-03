<div class="cart-item" style="opacity: 0.9;" id="saved-item-<?php echo $item['product']['id']; ?>">
    <div class="item-image" onclick="window.location.href='/product/<?php echo $item['product']['id']; ?>'">
        <?php echo $item['product']['icon']; ?>
    </div>
    <div class="item-details">
        <h3 class="item-name">
            <?php echo $item['product']['name']; ?>
        </h3>
        <p class="item-price">
            <?php echo $item['formatted_price']; ?>
        </p>
        <button class="btn btn-sm btn-outline" onclick="moveToCartFromSaved(<?php echo $item['product']['id']; ?>)"
            style="margin-top: 0.5rem;">Move to Cart</button>
    </div>
    <div class="item-total"></div>
</div>

