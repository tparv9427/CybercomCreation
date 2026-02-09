<!-- Brands -->
<div class="filter-section">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem;">
        <h4 class="filter-title" style="margin-bottom: 0;">Brand</h4>
        <button onclick="openBrandModal(event)"
            style="background: none; border: none; color: var(--accent); font-size: 0.8rem; font-weight: 600; cursor: pointer;">View
            All</button>
    </div>
    <ul class="filter-list">

        <?php
        $displayBrands = [];
        // First, add selected brands up to 4
        foreach ($brand_ids as $sid) {
            if (count($displayBrands) >= 4)
                break;
            $sBrand = $this->brandRepo->find((int) $sid);
            if ($sBrand) {
                $displayBrands[] = $sBrand;
            }
        }

        // Then, fill remaining slots with top brands up to total 4
        foreach ($brands as $brand) {
            if (count($displayBrands) >= 4)
                break;
            if (!in_array($brand, $displayBrands)) {
                $displayBrands[] = $brand;
            }
        }
        ?>
        <?php foreach ($displayBrands as $brand): ?>
            <li class="filter-item <?php echo in_array($brand['id'], $brand_ids) ? 'active' : ''; ?>">
                <a
                    href="/products?brand[]=<?php echo $brand['id']; ?><?php echo $category_id ? '&category=' . $category_id : ''; ?>">
                    <?php echo $brand['name']; ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</div>