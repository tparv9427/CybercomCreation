/**
 * Product Detail Page Interactive Logic
 * Handles tabs, quantity controls, variant selection, and image switching.
 */

// Tab Switching
function switchTab(index) {
    const tabs = document.querySelectorAll('.tab');
    const contents = document.querySelectorAll('.tab-content');

    tabs.forEach(t => t.classList.remove('active'));
    contents.forEach(c => c.classList.remove('active'));

    if (tabs[index] && contents[index]) {
        tabs[index].classList.add('active');
        contents[index].classList.add('active');
    }
}

// Quantity Controls
function increaseQty() {
    const input = document.getElementById('quantity');
    if (!input) return;

    const current = parseInt(input.value) || 0;
    const max = parseInt(input.max) || 999;

    if (current < max) {
        input.value = current + 1;
    } else {
        if (typeof validateMaxStock === 'function') {
            validateMaxStock(input);
        }
    }
}

function decreaseQty() {
    const input = document.getElementById('quantity');
    if (!input) return;

    if (parseInt(input.value) > 1) {
        input.value = parseInt(input.value) - 1;
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // Variant Selection
    const variantBtns = document.querySelectorAll('.variant-btn');
    variantBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            // Remove active class from siblings in the same group
            const siblings = this.parentElement.querySelectorAll('.variant-btn');
            siblings.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Thumbnail Switching
    const thumbnails = document.querySelectorAll('.thumbnail');
    const mainImageContainer = document.querySelector('.main-image');

    if (thumbnails.length > 0 && mainImageContainer) {
        thumbnails.forEach(thumb => {
            thumb.addEventListener('click', function () {
                // Update active state
                thumbnails.forEach(t => t.classList.remove('active'));
                this.classList.add('active');

                // Update main image content
                // Note: In a real implementation, this would swap the src attribute
                // For now, we'll swap the innerHTML if it's an SVG/div, or src if it's an img
                const thumbContent = this.innerHTML;
                if (thumbContent) {
                    mainImageContainer.innerHTML = thumbContent;
                }
            });
        });
    }

    // Attach event listeners to quantity buttons if they exist
    // (This avoids inline onclick attributes in HTML in the future)
    /*
    const increaseBtn = document.querySelector('.quantity-btn-increase');
    const decreaseBtn = document.querySelector('.quantity-btn-decrease');
    if (increaseBtn) increaseBtn.addEventListener('click', increaseQty);
    if (decreaseBtn) decreaseBtn.addEventListener('click', decreaseQty);
    */
});
