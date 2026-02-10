<!-- Price Range -->
<div class="filter-section">
    <h4 class="filter-title">Price Range</h4>
    <div class="filter-list">
        <?php
        $priceOptions = [
            'under50' => 'Under $50',
            '50-100' => '$50 to $100',
            '100-200' => '$100 to $200',
            '200plus' => '$200 & Above'
        ];
        foreach ($priceOptions as $value => $label):
            $isActive = $price_range == $value || (!$price_range && $value == '');
            $url = '/products?';
            if ($category_id)
                $url .= 'category=' . $category_id . '&';
            if (!empty($brand_ids)) {
                foreach ($brand_ids as $bid) {
                    $url .= 'brand[]=' . $bid . '&';
                }
            }
            if ($value)
                $url .= 'price=' . $value;
            ?>
            <div class="filter-item <?php echo $isActive ? 'active' : ''; ?>">
                <label>
                    <input type="radio" name="price" class="filter-checkbox" <?php echo $isActive ? 'checked' : ''; ?>
                        onclick="window.location.href='<?php echo $url; ?>'">
                    <span>
                        <?php echo $label; ?>
                    </span>
                </label>
            </div>
        <?php endforeach; ?>
    </div>
</div>