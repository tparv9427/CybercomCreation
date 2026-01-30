<!-- Categories -->
<div class="filter-section">
    <h4 class="filter-title">Category</h4>
    <ul class="filter-list">
        <li class="filter-item <?php echo !$category_id ? 'active' : ''; ?>">
            <a href="products.php">All Products</a>
        </li>
        <?php foreach ($categories as $cat): ?>
            <li class="filter-item <?php echo $category_id == $cat['id'] ? 'active' : ''; ?>">
                <a href="products.php?category=<?php echo $cat['id']; ?>">
                    <?php echo $cat['name']; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<!-- Price Range -->
<div class="filter-section">
    <h4 class="filter-title">Price Range</h4>
    <div class="filter-list">
        <?php
        $priceOptions = [
            '' => 'Any Price',
            'under50' => 'Under $50',
            '50-100' => '$50 to $100',
            '100-200' => '$100 to $200',
            '200plus' => '$200 & Above'
        ];
        foreach ($priceOptions as $value => $label):
            $isActive = $price_range == $value || (!$price_range && $value == '');
            $url = 'products.php?';
            if ($category_id)
                $url .= 'category=' . $category_id . '&';
            if ($value)
                $url .= 'price=' . $value;
            ?>
            <div class="filter-item <?php echo $isActive ? 'active' : ''; ?>">
                <input type="radio" name="price" class="filter-checkbox" <?php echo $isActive ? 'checked' : ''; ?>
                onclick="window.location.href='
            <?php echo $url; ?>'">
                <span onclick="window.location.href='<?php echo $url; ?>'">
                    <?php echo $label; ?>
                </span>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Brands -->
<div class="filter-section">
    <h4 class="filter-title">Brand</h4>
    <ul class="filter-list">
        <li class="filter-item <?php echo !$brand_id ? 'active' : ''; ?>">
            <a href="products.php<?php echo $category_id ? '?category=' . $category_id : ''; ?>">All Brands</a>
        </li>
        <?php foreach ($brands as $brand): ?>
            <li class="filter-item <?php echo $brand_id == $brand['id'] ? 'active' : ''; ?>">
                <a
                    href="products.php?brand=<?php echo $brand['id']; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?>">
                    <?php echo $brand['name']; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<!-- Rating -->
<div class="filter-section">
    <h4 class="filter-title">Avg. Customer Review</h4>
    <div class="filter-list">
        <?php for ($i = 4; $i >= 3; $i--):
            $isActive = $rating_filter == $i;
            $url = 'products.php?';
            if ($category_id)
                $url .= 'category=' . $category_id . '&';
            if ($brand_id)
                $url .= 'brand=' . $brand_id . '&';
            $url .= 'rating=' . $i;
            ?>
            <div class="rating-item" onclick="window.location.href='<?php echo $url; ?>'">
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