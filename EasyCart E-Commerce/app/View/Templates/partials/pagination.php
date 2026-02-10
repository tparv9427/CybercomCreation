<?php if (isset($total_pages) && $total_pages > 1): ?>
    <?php
    // Ensure helper function is available or defined
    if (!isset($buildUrl)) {
        $buildUrl = function ($page) {
            $params = $_GET;
            $params['page'] = $page;
            return '?' . http_build_query($params);
        };
    }

    // Default margin style
    $marginStyle = isset($style) ? $style : '';
    ?>

    <div class="pagination" style="<?php echo $marginStyle; ?>">
        <!-- Previous -->
        <a href="<?php echo $buildUrl($current_page - 1); ?>"
            class="page-link pagination-link <?php echo $current_page <= 1 ? 'disabled' : ''; ?>"
            data-page="<?php echo $current_page - 1; ?>" <?php echo $current_page <= 1 ? 'onclick="return false;"' : ''; ?>>
            &lt;
        </a>

        <!-- Page Numbers -->
        <?php
        $range = [];
        $rangeWithDots = [];

        $current = $current_page;
        $total = $total_pages;

        // Configuration
        $middle_count = 7;
        $end_count = 3;

        // 1. Always show first 3 pages
        for ($i = 1; $i <= min($end_count, $total); $i++) {
            $range[] = $i;
        }

        // 2. Always show last 3 pages
        for ($i = max(1, $total - $end_count + 1); $i <= $total; $i++) {
            $range[] = $i;
        }

        // 3. Calculate middle window (7 pages centered on current)
        $half = floor($middle_count / 2);
        $start_window = max(1, $current - $half);
        $end_window = min($total, $current + $half);

        // Adjust window if near edges to keep 7 items if possible
        if ($start_window < 1 + $end_count) {
            $end_window = min($total, $end_window + ($end_count - $start_window + 1));
        }
        if ($end_window > $total - $end_count) {
            $start_window = max(1, $start_window - ($end_window - ($total - $end_count)));
        }

        for ($i = $start_window; $i <= $end_window; $i++) {
            $range[] = $i;
        }

        // 4. Sort and Unique
        $range = array_unique($range);
        sort($range);

        // 5. Build Output with Dots
        $l = null;
        foreach ($range as $i) {
            if ($l !== null) {
                if ($i - $l === 2) {
                    $rangeWithDots[] = $l + 1; // Fill single gap
                } elseif ($i - $l > 1) {
                    $rangeWithDots[] = '...';
                }
            }
            $rangeWithDots[] = $i;
            $l = $i;
        }
        ?>

        <?php foreach ($rangeWithDots as $page): ?>
            <?php if ($page === '...'): ?>
                <span class="page-link disabled" style="border: none; background: transparent;">...</span>
            <?php else: ?>
                <a href="<?php echo $buildUrl($page); ?>"
                    class="page-link pagination-link <?php echo $page == $current_page ? 'active' : ''; ?>"
                    data-page="<?php echo $page; ?>">
                    <?php echo $page; ?>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>

        <!-- Next -->
        <a href="<?php echo $buildUrl($current_page + 1); ?>"
            class="page-link pagination-link <?php echo $current_page >= $total_pages ? 'disabled' : ''; ?>"
            data-page="<?php echo $current_page + 1; ?>" <?php echo $current_page >= $total_pages ? 'onclick="return false;"' : ''; ?>>
            &gt;
        </a>
    </div>
<?php endif; ?>