<?php if (isset($total_pages) && $total_pages > 1): ?>
    <?php
    // Ensure helper function is available or defined
    if (!isset($buildUrl)) {
        $buildUrl = function($page) {
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
           data-page="<?php echo $current_page - 1; ?>"
           <?php echo $current_page <= 1 ? 'onclick="return false;"' : ''; ?>>
            &lt;
        </a>

        <!-- Page Numbers -->
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="<?php echo $buildUrl($i); ?>" 
               class="page-link pagination-link <?php echo $i == $current_page ? 'active' : ''; ?>"
               data-page="<?php echo $i; ?>">
                <?php echo $i; ?>
            </a>
        <?php endfor; ?>

        <!-- Next -->
        <a href="<?php echo $buildUrl($current_page + 1); ?>" 
           class="page-link pagination-link <?php echo $current_page >= $total_pages ? 'disabled' : ''; ?>"
           data-page="<?php echo $current_page + 1; ?>"
           <?php echo $current_page >= $total_pages ? 'onclick="return false;"' : ''; ?>>
            &gt;
        </a>
    </div>
<?php endif; ?>
