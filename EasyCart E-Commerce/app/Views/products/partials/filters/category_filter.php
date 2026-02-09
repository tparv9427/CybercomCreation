<!-- Categories -->
<div class="filter-section">
    <h4 class="filter-title">Category</h4>
    <ul class="filter-list">

        <?php foreach ($categories as $cat): ?>
            <li class="filter-item <?php echo $category_id == $cat['id'] ? 'active' : ''; ?>">
                <a href="/products?category=<?php echo $cat['id']; ?><?php
                   if (!empty($brand_ids)) {
                       foreach ($brand_ids as $bid) {
                           echo '&brand[]=' . $bid;
                       }
                   }
                   ?>">
                    <?php echo $cat['name']; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>