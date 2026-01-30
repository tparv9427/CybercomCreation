<!-- Top Control Bar -->
<div class="plp-header">
    <div class="results-count">
        Showing
        <?php echo $product_count; ?> product
        <?php echo $product_count != 1 ? 's' : ''; ?>
    </div>

    <div class="plp-controls">
        <!-- Sort Dropdown -->
        <div class="sort-wrapper">
            <label for="sort" style="font-size: 0.9rem; color: #555;">Sort by:</label>
            <select id="sort" class="sort-select" onchange="window.location.href=this.value">
                <?php
                $baseUrl = 'products.php?';
                if ($category_id)
                    $baseUrl .= 'category=' . $category_id . '&';
                if ($brand_id)
                    $baseUrl .= 'brand=' . $brand_id . '&';
                if ($price_range)
                    $baseUrl .= 'price=' . $price_range . '&';
                if ($rating_filter)
                    $baseUrl .= 'rating=' . $rating_filter . '&';
                $currentSort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';
                ?>
                <option value="<?php echo $baseUrl . 'sort=newest'; ?>" <?php echo $currentSort == 'newest' ? 'selected' : ''; ?>>Newest Arrivals</option>
                <option value="<?php echo $baseUrl . 'sort=price_low'; ?>" <?php echo $currentSort == 'price_low' ? 'selected' : ''; ?>>Price: Low to High</option>
                <option value="<?php echo $baseUrl . 'sort=price_high'; ?>" <?php echo $currentSort == 'price_high' ? 'selected' : ''; ?>>Price: High to Low</option>
                <option value="<?php echo $baseUrl . 'sort=rating'; ?>" <?php echo $currentSort == 'rating' ? 'selected' : ''; ?>>Avg. Customer Review</option>
            </select>
        </div>

        <!-- View Toggle -->
        <div class="view-toggle">
            <button class="view-btn active" onclick="toggleView('grid')" title="Grid View">⊞</button>
            <button class="view-btn" onclick="toggleView('row')" title="List View">☰</button>
        </div>
    </div>
</div>