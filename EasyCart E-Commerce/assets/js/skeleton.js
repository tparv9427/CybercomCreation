/**
 * EasyCart Skeleton Loading Functions v4.0-fake-server
 * Show/hide skeleton loading states
 */

// ==================== SKELETON DISPLAY FUNCTIONS ====================

/**
 * Show product grid skeleton
 */
function showProductGridSkeleton(container, count = 6) {
    const skeletonHTML = generateProductCardSkeletons(count);
    container.innerHTML = skeletonHTML;
    container.classList.add('skeleton-visible');
}

/**
 * Hide product grid skeleton and show real content
 */
function hideProductGridSkeleton(container, realContent) {
    container.classList.add('fade-out');
    setTimeout(() => {
        container.innerHTML = realContent;
        container.classList.remove('fade-out', 'skeleton-visible');
        container.classList.add('fade-in');
    }, 200);
}

/**
 * Generate product card skeletons HTML
 */
function generateProductCardSkeletons(count) {
    let html = '';
    for (let i = 0; i < count; i++) {
        html += `
        <div class="skeleton-product-card">
            <div class="skeleton-product-image skeleton"></div>
            <div class="skeleton-product-info">
                <div class="skeleton-category skeleton"></div>
                <div class="skeleton-product-name skeleton"></div>
                <div class="skeleton-product-name-line2 skeleton"></div>
                <div class="skeleton-price skeleton"></div>
                <div class="skeleton-rating skeleton"></div>
            </div>
        </div>`;
    }
    return html;
}

/**
 * Show cart skeleton
 */
function showCartSkeleton(container, count = 3) {
    const skeletonHTML = generateCartSkeletons(count);
    container.innerHTML = skeletonHTML;
}

/**
 * Generate cart item skeletons
 */
function generateCartSkeletons(count) {
    let html = '';
    for (let i = 0; i < count; i++) {
        html += `
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
        </div>`;
    }
    return html;
}

/**
 * Show loading overlay
 */
function showLoadingOverlay(message = 'Loading...') {
    // Remove existing overlay if any
    hideLoadingOverlay();
    
    const overlay = document.createElement('div');
    overlay.id = 'loadingOverlay';
    overlay.className = 'loading-overlay';
    overlay.innerHTML = `
        <div class="loading-overlay-content">
            <div class="loading-overlay-spinner"></div>
            <div class="loading-overlay-text">${message}</div>
        </div>
    `;
    document.body.appendChild(overlay);
}

/**
 * Hide loading overlay
 */
function hideLoadingOverlay() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.classList.add('fade-out');
        setTimeout(() => overlay.remove(), 300);
    }
}

/**
 * Show button loading state
 */
function showButtonLoading(button) {
    if (button) {
        button.classList.add('btn-loading');
        button.disabled = true;
        button.dataset.originalText = button.textContent;
    }
}

/**
 * Hide button loading state
 */
function hideButtonLoading(button) {
    if (button) {
        button.classList.remove('btn-loading');
        button.disabled = false;
        if (button.dataset.originalText) {
            button.textContent = button.dataset.originalText;
        }
    }
}

/**
 * Show inline spinner
 */
function showSpinner(container, size = 'normal') {
    const spinner = document.createElement('span');
    spinner.className = size === 'large' ? 'loading-spinner large' : 'loading-spinner';
    spinner.id = 'inlineSpinner';
    container.appendChild(spinner);
}

/**
 * Hide inline spinner
 */
function hideSpinner() {
    const spinner = document.getElementById('inlineSpinner');
    if (spinner) {
        spinner.remove();
    }
}

/**
 * Show search skeleton
 */
function showSearchSkeleton(container, count = 5) {
    let html = '';
    for (let i = 0; i < count; i++) {
        html += `
        <div class="skeleton-search-result">
            <div class="skeleton-search-image skeleton"></div>
            <div class="skeleton-search-info">
                <div class="skeleton-text short skeleton"></div>
                <div class="skeleton-text long skeleton"></div>
                <div class="skeleton-text medium skeleton"></div>
                <div class="skeleton-text short skeleton"></div>
            </div>
        </div>`;
    }
    container.innerHTML = html;
}

/**
 * Show product details skeleton
 */
function showProductDetailsSkeleton(container) {
    container.innerHTML = `
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
    </div>`;
}

/**
 * Replace skeleton with real content
 */
function replaceSkeletonWithContent(skeleton, realContent) {
    if (skeleton && realContent) {
        skeleton.classList.add('fade-out');
        setTimeout(() => {
            skeleton.replaceWith(realContent);
            realContent.classList.add('fade-in');
        }, 200);
    }
}

/**
 * Show badge loading skeleton
 */
function showBadgeSkeleton(badge) {
    if (badge) {
        badge.classList.add('skeleton-badge', 'skeleton');
        badge.textContent = '';
    }
}

/**
 * Hide badge skeleton
 */
function hideBadgeSkeleton(badge, count) {
    if (badge) {
        badge.classList.remove('skeleton-badge', 'skeleton');
        badge.textContent = count;
    }
}

// ==================== UTILITY FUNCTIONS ====================

/**
 * Delay function (for testing)
 */
function delay(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

/**
 * Simulate loading with skeleton
 */
async function simulateLoading(showFunc, hideFunc, duration = 1000) {
    showFunc();
    await delay(duration);
    hideFunc();
}
