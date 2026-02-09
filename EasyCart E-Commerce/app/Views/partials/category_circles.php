<?php
/**
 * Category Circles Component
 * 
 * Renders a dynamic row of category circles with icons.
 * Max 6 circles total. Shows the "Main Arrow Circle" at the end.
 */

$categories = $categories ?? [];
$total_categories_count = count($categories);
$max_total_circles = 6;

// Convert to indexed array
$categories_list = array_values($categories);

// Enhanced Icon mapping (Clean, theme-matching Lucide-style SVGs)
$category_icons = [
    'Electronics' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="8" y1="21" x2="16" y2="21"></line><line x1="12" y1="17" x2="12" y2="21"></line></svg>',
    'Laptops' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"></rect><line x1="2" y1="20" x2="22" y2="20"></line></svg>',
    'Smartphones' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"></rect><path d="M12 18h.01"></path></svg>',
    'Fashion' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.38 3.46L16 2a4 4 0 01-8 0L3.62 3.46a2 2 0 00-1.62 1.96V10a6 6 0 0012 0V5.46a2 2 0 014 0V10a6 6 0 0012 0V5.42a2 2 0 00-1.62-1.96z"></path></svg>',
    'Home & Living' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path><polyline points="9 22 9 12 15 12 15 22"></polyline></svg>',
    'Sports' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 12a6 6 0 1 1 12 0 6 6 0 0 1-12 0Z"></path><path d="M12 6v12"></path><path d="M6 12h12"></path></svg>',
    'Books' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1-2.5-2.5Z"></path><path d="M4 19.5E2.5 2.5 0 0 1 6.5 17H20"></path></svg>',
    'Accessories' => '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>',
];

$default_icon = '<svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="16"></line><line x1="8" y1="12" x2="16" y2="12"></line></svg>';
// This is the Main Arrow Circle icon
$view_all_icon = '<svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>';
?>

<div class="category-circles-container">
    <div class="category-circles-wrapper">
        <div class="category-circles-grid" id="categoryCirclesGrid">
            <?php
            // Render all categories (JS will handle hiding ones that don't fit)
            // Limited to max total - 1 to leave room for the arrow
            foreach ($categories_list as $index => $category): ?>
                <div class="category-circle-item cat-node" data-index="<?php echo $index; ?>">
                    <a href="/products?category=<?php echo $category['id']; ?>" class="category-circle-link">
                        <div class="category-circle">
                            <div class="category-icon">
                                <?php echo $category_icons[$category['name']] ?? $default_icon; ?>
                            </div>
                        </div>
                        <span class="category-circle-label"><?php echo htmlspecialchars($category['name']); ?></span>
                    </a>
                </div>
            <?php endforeach; ?>

            <!-- The Main Arrow Circle (Always Present) -->
            <div class="category-circle-item view-all-item" id="viewAllCategory">
                <a href="/products" class="category-circle-link">
                    <div class="category-circle view-all">
                        <div class="category-icon view-all-arrow">
                            <?php echo $view_all_icon; ?>
                        </div>
                    </div>
                    <span class="category-circle-label">More</span>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    .category-circles-container {
        padding: 2rem 0;
        overflow: hidden;
        max-width: 1400px;
        margin: 0 auto;
    }

    .category-circles-wrapper {
        display: flex;
        justify-content: center;
        padding: 1rem 2rem;
        min-height: 180px;
    }

    .category-circles-grid {
        display: flex;
        gap: 2.5rem;
        align-items: flex-start;
        transition: all 0.4s ease;
    }

    .category-circle-item {
        flex: 0 0 auto;
        width: 120px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .category-circle-link {
        text-decoration: none;
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1.25rem;
    }

    .category-circle {
        width: 110px;
        height: 110px;
        background: #F3F4F6;
        /* Matches the Reference Screenshot's light gray */
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        border: 1px solid transparent;
    }

    [data-theme="dark"] .category-circle {
        background: #1E293B;
    }

    .category-circle-item:hover .category-circle {
        transform: scale(1.05);
        background: white;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        border-color: var(--border);
    }

    [data-theme="dark"] .category-circle-item:hover .category-circle {
        background: #334155;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }

    .category-icon {
        color: #2D3142;
        /* Dark theme-matching icon color from screenshot */
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    [data-theme="dark"] .category-icon {
        color: #E2E8F0;
    }

    .category-circle-item:hover .category-icon {
        color: var(--accent);
    }

    /* Main Arrow Circle Style */
    .category-circle.view-all {
        background: #F3F4F6;
        border: 1px solid #E5E7EB;
    }

    [data-theme="dark"] .category-circle.view-all {
        background: #1E293B;
        border-color: #334155;
    }

    .view-all-arrow {
        color: #2D3142;
    }

    [data-theme="dark"] .view-all-arrow {
        color: #E2E8F0;
    }

    .category-circle-item:hover .category-circle.view-all {
        background: var(--primary);
        border-color: var(--primary);
    }

    .category-circle-item:hover .category-circle.view-all .view-all-arrow {
        color: white;
    }

    .category-circle-label {
        font-size: 0.9rem;
        font-weight: 600;
        color: #2D3142;
        text-align: center;
        transition: color 0.3s;
        white-space: nowrap;
    }

    [data-theme="dark"] .category-circle-label {
        color: #E2E8F0;
    }

    .category-circle-item:hover .category-circle-label {
        color: var(--accent);
    }

    /* Responsive Logic */
    @media (max-width: 1400px) {
        .category-circles-grid {
            gap: 2rem;
        }
    }

    @media (max-width: 900px) {
        .category-circles-grid {
            gap: 1.5rem;
        }

        .category-circle {
            width: 100px;
            height: 100px;
        }

        .category-circle-item {
            width: 100px;
        }
    }
</style>

<script>
    /**
     * Dynamic Categories View Handler
     * Ensures the Arrow Circle is always present as the last item.
     */
    (function () {
        function updateCategoryView() {
            const wrapper = document.querySelector('.category-circles-wrapper');
            const grid = document.getElementById('categoryCirclesGrid');
            if (!wrapper || !grid) return;

            const items = grid.querySelectorAll('.cat-node');
            const viewAll = document.getElementById('viewAllCategory');
            const availableWidth = wrapper.offsetWidth;

            const maxTotalAllowed = 6;
            const estimatedItemWidth = 145; // Item width + margin

            // Calculate slots available in viewport, capped at 6
            let totalSlots = Math.floor(availableWidth / estimatedItemWidth);
            if (totalSlots > maxTotalAllowed) totalSlots = maxTotalAllowed;
            if (totalSlots < 1) totalSlots = 1;

            // Requirement: One slot MUST be the View All arrow.
            // So we have (totalSlots - 1) slots for category icons.
            let catSlotsCount = totalSlots - 1;

            // If we only have ONE slot total, catSlotsCount becomes 0.
            // Only the View All arrow will be shown.

            items.forEach((item, index) => {
                if (index < catSlotsCount) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });

            // The View All arrow is always displayed as long as we have at least 1 slot available
            viewAll.style.display = (totalSlots >= 1) ? 'block' : 'none';

            // Center the grid elements
            grid.style.justifyContent = 'center';
        }

        window.addEventListener('resize', updateCategoryView);
        document.addEventListener('DOMContentLoaded', updateCategoryView);

        // Initial call with stabilization
        setTimeout(updateCategoryView, 150);
    })();
</script>