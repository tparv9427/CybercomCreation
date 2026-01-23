<?php
/**
 * EasyCart Skeleton Components v4.0-fake-server
 * Reusable skeleton loading templates
 */

// Product Card Skeleton
function renderProductCardSkeleton($count = 6) {
    $html = '';
    for ($i = 0; $i < $count; $i++) {
        $html .= '
        <div class="skeleton-product-card">
            <div class="skeleton-product-image skeleton"></div>
            <div class="skeleton-product-info">
                <div class="skeleton-category skeleton"></div>
                <div class="skeleton-product-name skeleton"></div>
                <div class="skeleton-product-name-line2 skeleton"></div>
                <div class="skeleton-price skeleton"></div>
                <div class="skeleton-rating skeleton"></div>
            </div>
        </div>';
    }
    return $html;
}

// Product Details Skeleton
function renderProductDetailsSkeleton() {
    return '
    <div class="skeleton-product-main">
        <div>
            <div class="skeleton-main-image skeleton"></div>
            <div class="skeleton-thumbnails">
                <div class="skeleton-thumbnail skeleton"></div>
                <div class="skeleton-thumbnail skeleton"></div>
                <div class="skeleton-thumbnail skeleton"></div>
                <div class="skeleton-thumbnail skeleton"></div>
            </div>
        </div>
        <div class="skeleton-product-details">
            <div class="skeleton-text short skeleton"></div>
            <div class="skeleton-title skeleton"></div>
            <div class="skeleton-text medium skeleton"></div>
            <div class="skeleton-text medium skeleton"></div>
            <div class="skeleton-detail-block">
                <div class="skeleton-text full skeleton"></div>
                <div class="skeleton-text full skeleton" style="margin-top: 0.5rem;"></div>
                <div class="skeleton-text medium skeleton" style="margin-top: 0.5rem;"></div>
            </div>
            <div class="skeleton-text long skeleton"></div>
            <div class="skeleton-text medium skeleton"></div>
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <div class="skeleton-button skeleton" style="flex: 1;"></div>
                <div class="skeleton-button skeleton" style="flex: 1;"></div>
            </div>
        </div>
    </div>';
}

// Cart Item Skeleton
function renderCartItemSkeleton($count = 3) {
    $html = '';
    for ($i = 0; $i < $count; $i++) {
        $html .= '
        <div class="skeleton-cart-item">
            <div class="skeleton-cart-image skeleton"></div>
            <div class="skeleton-cart-details">
                <div class="skeleton-text medium skeleton"></div>
                <div class="skeleton-text short skeleton"></div>
                <div class="skeleton-text short skeleton"></div>
            </div>
            <div class="skeleton-quantity skeleton"></div>
            <div class="skeleton-cart-price skeleton"></div>
            <div class="skeleton-remove-btn skeleton"></div>
        </div>';
    }
    return $html;
}

// Search Result Skeleton
function renderSearchResultSkeleton($count = 5) {
    $html = '';
    for ($i = 0; $i < $count; $i++) {
        $html .= '
        <div class="skeleton-search-result">
            <div class="skeleton-search-image skeleton"></div>
            <div class="skeleton-search-info">
                <div class="skeleton-text short skeleton"></div>
                <div class="skeleton-text long skeleton"></div>
                <div class="skeleton-text medium skeleton"></div>
                <div class="skeleton-text short skeleton"></div>
            </div>
        </div>';
    }
    return $html;
}

// Form Skeleton
function renderFormSkeleton($fields = 4) {
    $html = '';
    for ($i = 0; $i < $fields; $i++) {
        $html .= '
        <div class="skeleton-form-group">
            <div class="skeleton-label skeleton"></div>
            <div class="skeleton-input skeleton"></div>
        </div>';
    }
    return $html;
}

// Table Row Skeleton
function renderTableRowSkeleton($columns = 5, $rows = 3) {
    $html = '<table class="skeleton-table"><tbody>';
    for ($i = 0; $i < $rows; $i++) {
        $html .= '<tr class="skeleton-table-row">';
        for ($j = 0; $j < $columns; $j++) {
            $width = ['short', 'medium', 'long', 'full'][array_rand(['short', 'medium', 'long', 'full'])];
            $html .= '<td class="skeleton-table-cell"><div class="skeleton-text ' . $width . ' skeleton"></div></td>';
        }
        $html .= '</tr>';
    }
    $html .= '</tbody></table>';
    return $html;
}

// Loading Overlay
function renderLoadingOverlay($text = 'Loading...') {
    return '
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-overlay-content">
            <div class="loading-overlay-spinner"></div>
            <div class="loading-overlay-text">' . htmlspecialchars($text) . '</div>
        </div>
    </div>';
}

// Inline Spinner
function renderSpinner($size = 'normal') {
    $class = $size === 'large' ? 'loading-spinner large' : 'loading-spinner';
    return '<span class="' . $class . '"></span>';
}

// Badge Skeleton
function renderBadgeSkeleton() {
    return '<span class="skeleton-badge skeleton"></span>';
}
?>
