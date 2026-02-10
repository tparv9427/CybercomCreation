<!-- Rating -->
<div class="filter-section">
    <h4 class="filter-title">Avg. Customer Review</h4>
    <div class="filter-list">
        <?php for ($i = 4; $i >= 3; $i--):
            $isActive = $rating_filter == $i;
            $url = '/products?';
            if ($category_id)
                $url .= 'category=' . $category_id . '&';
            if (!empty($brand_ids)) {
                foreach ($brand_ids as $bid) {
                    $url .= 'brand[]=' . $bid . '&';
                }
            }
            $url .= 'rating=' . $i;
            ?>
            <div class="rating-item <?php echo $isActive ? 'active' : ''; ?>"
                onclick="window.location.href='<?php echo $url; ?>'">
                <div class="rating-stars">
                    <?php
                    for ($j = 0; $j < 5; $j++)
                        echo $j < $i ? '★' : '☆';
                    ?>
                </div>
                <span style="<?php echo $isActive ? 'font-weight:bold;color:var(--primary);' : ''; ?>">& Up</span>
            </div>
        <?php endfor; ?>
    </div>
</div>