// ============================================
// ENHANCED MAIN.JS - PHASE 3 COMPLETE
// ============================================

// Dark Mode Toggle
const themeToggle = document.getElementById('themeToggle');
const html = document.documentElement;

const currentTheme = localStorage.getItem('theme') || 'light';
html.setAttribute('data-theme', currentTheme);

themeToggle?.addEventListener('click', function () {
    const theme = html.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
    html.setAttribute('data-theme', theme);
    localStorage.setItem('theme', theme);
});

// ============================================
// MOBILE MENU
// ============================================
const mobileMenuToggle = document.getElementById('mobileMenuToggle');
const mainNav = document.getElementById('mainNav');

mobileMenuToggle?.addEventListener('click', function () {
    this.classList.toggle('active');
    mainNav.classList.toggle('active');
});

document.addEventListener('click', function (event) {
    if (!event.target.closest('.header-container')) {
        mobileMenuToggle?.classList.remove('active');
        mainNav?.classList.remove('active');
    }
});

// ============================================
// SEARCH FUNCTIONALITY
// ============================================
function performSearch() {
    const searchInput = document.getElementById('searchInput');
    const query = searchInput.value.trim();

    if (query) {
        window.location.href = 'search.php?q=' + encodeURIComponent(query);
    }
}

document.getElementById('searchInput')?.addEventListener('keypress', function (e) {
    if (e.key === 'Enter') {
        performSearch();
    }
});

// ============================================
// VIEW TOGGLE (GRID/ROW)
// ============================================
function toggleView(view) {
    const gridView = document.getElementById('gridView');
    const rowView = document.getElementById('rowView');
    const viewBtns = document.querySelectorAll('.view-btn');

    viewBtns.forEach(btn => btn.classList.remove('active'));

    if (view === 'grid') {
        gridView.style.display = 'grid';
        rowView.classList.remove('active');
        viewBtns[0]?.classList.add('active');
        localStorage.setItem('view', 'grid');
    } else {
        gridView.style.display = 'none';
        rowView.classList.add('active');
        viewBtns[1]?.classList.add('active');
        localStorage.setItem('view', 'row');
    }
}

const savedView = localStorage.getItem('view');
if (savedView && savedView === 'row') {
    toggleView('row');
}

// ============================================
// ENHANCED BANNER CAROUSEL WITH DOTS
// ============================================
document.addEventListener('DOMContentLoaded', function () {
    const carousel = document.querySelector('.banner-carousel');
    if (!carousel) return;

    const slides = carousel.querySelectorAll('.banner-slide');
    if (slides.length === 0) return;

    let currentSlide = 0;
    const slideCount = slides.length;
    const slideInterval = 2000; // 7 seconds per slide

    // Create dots container
    const dotsContainer = document.createElement('div');
    dotsContainer.className = 'carousel-dots';
    carousel.appendChild(dotsContainer);

    // Create dots
    for (let i = 0; i < slideCount; i++) {
        const dot = document.createElement('button');
        dot.className = 'carousel-dot';
        dot.setAttribute('aria-label', `Go to slide ${i + 1}`);
        if (i === 0) dot.classList.add('active');
        dot.addEventListener('click', () => goToSlide(i));
        dotsContainer.appendChild(dot);
    }

    const dots = dotsContainer.querySelectorAll('.carousel-dot');

    // Initialize slides
    slides.forEach((slide, index) => {
        slide.style.position = 'absolute';
        slide.style.width = '100%';
        slide.style.height = '100%';
        slide.style.transition = 'transform 0.8s ease-in-out, opacity 0.8s ease-in-out';

        if (index === 0) {
            slide.style.transform = 'translateX(0)';
            slide.style.opacity = '1';
            slide.style.zIndex = '2';
        } else {
            slide.style.transform = 'translateX(100%)';
            slide.style.opacity = '0';
            slide.style.zIndex = '1';
        }
    });

    function goToSlide(index) {
        if (index === currentSlide) return;

        const currentSlideEl = slides[currentSlide];
        const nextSlideEl = slides[index];

        // Animate current slide out (left)
        currentSlideEl.style.transform = 'translateX(-100%)';
        currentSlideEl.style.opacity = '0';
        currentSlideEl.style.zIndex = '1';

        // Animate next slide in (from right)
        nextSlideEl.style.transform = 'translateX(0)';
        nextSlideEl.style.opacity = '1';
        nextSlideEl.style.zIndex = '2';

        // Update dots
        dots[currentSlide].classList.remove('active');
        dots[index].classList.add('active');

        currentSlide = index;
    }

    function nextSlide() {
        const next = (currentSlide + 1) % slideCount;
        goToSlide(next);
    }

    // Auto-advance
    let autoSlide = setInterval(nextSlide, slideInterval);

    // Pause on hover
    carousel.addEventListener('mouseenter', () => {
        clearInterval(autoSlide);
    });

    carousel.addEventListener('mouseleave', () => {
        autoSlide = setInterval(nextSlide, slideInterval);
    });
});

// ============================================
// ENHANCED DROPDOWN WITH DELAY
// ============================================
document.addEventListener('DOMContentLoaded', function () {
    const dropdowns = document.querySelectorAll('.dropdown');

    dropdowns.forEach(dropdown => {
        let closeTimeout;
        const menu = dropdown.querySelector('.dropdown-menu');

        dropdown.addEventListener('mouseenter', () => {
            clearTimeout(closeTimeout);
            menu.style.display = 'block';
        });

        dropdown.addEventListener('mouseleave', () => {
            closeTimeout = setTimeout(() => {
                menu.style.display = 'none';
            }, 300); // 300ms delay before closing
        });
    });
});

// ============================================
// PRODUCT COUNT ON LISTING PAGE
// ============================================
function updateProductCount() {
    const resultsCount = document.querySelector('.results-count');
    if (!resultsCount) return;

    const gridView = document.getElementById('gridView');
    const rowView = document.getElementById('rowView');

    let visibleProducts = 0;

    if (gridView && gridView.style.display !== 'none') {
        visibleProducts = gridView.querySelectorAll('.product-card:not([style*="display: none"])').length;
    } else if (rowView && rowView.classList.contains('active')) {
        visibleProducts = rowView.querySelectorAll('.product-row-item:not([style*="display: none"])').length;
    }

    resultsCount.textContent = `Showing ${visibleProducts} product${visibleProducts !== 1 ? 's' : ''}`;
}

// Call on page load
document.addEventListener('DOMContentLoaded', updateProductCount);

// ============================================
// FORM VALIDATIONS
// ============================================

// Email validation
function validateEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

// Phone validation (10 digits)
function validatePhone(phone) {
    const re = /^\d{10}$/;
    return re.test(phone.replace(/\D/g, ''));
}

// Zip code validation (6 digits for India)
function validateZip(zip) {
    const re = /^\d{6}$/;
    return re.test(zip);
}

// Show error message
function showFieldError(input, message) {
    input.classList.add('error');

    let errorDiv = input.parentElement.querySelector('.field-error');
    if (!errorDiv) {
        errorDiv = document.createElement('div');
        errorDiv.className = 'field-error';
        input.parentElement.appendChild(errorDiv);
    }
    errorDiv.textContent = message;
    errorDiv.style.display = 'block';
}

// Clear error message
function clearFieldError(input) {
    input.classList.remove('error');
    const errorDiv = input.parentElement.querySelector('.field-error');
    if (errorDiv) {
        errorDiv.style.display = 'none';
    }
}

// Login Form Validation
document.addEventListener('DOMContentLoaded', function () {
    const loginForm = document.querySelector('form[action*="login"]');
    if (loginForm) {
        const emailInput = loginForm.querySelector('input[type="email"]');
        const passwordInput = loginForm.querySelector('input[type="password"]');

        emailInput?.addEventListener('blur', function () {
            if (!this.value) {
                showFieldError(this, 'Email is required');
            } else if (!validateEmail(this.value)) {
                showFieldError(this, 'Please enter a valid email address');
            } else {
                clearFieldError(this);
            }
        });

        emailInput?.addEventListener('input', function () {
            if (this.value && validateEmail(this.value)) {
                clearFieldError(this);
            }
        });

        passwordInput?.addEventListener('blur', function () {
            if (!this.value) {
                showFieldError(this, 'Password is required');
            } else {
                clearFieldError(this);
            }
        });

        loginForm.addEventListener('submit', function (e) {
            let isValid = true;

            if (!emailInput.value || !validateEmail(emailInput.value)) {
                showFieldError(emailInput, 'Please enter a valid email address');
                isValid = false;
            }

            if (!passwordInput.value) {
                showFieldError(passwordInput, 'Password is required');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    }
});

// Signup Form Validation
document.addEventListener('DOMContentLoaded', function () {
    const signupForm = document.querySelector('form[action*="signup"]');
    if (signupForm) {
        const nameInput = signupForm.querySelector('input[name="name"]');
        const emailInput = signupForm.querySelector('input[name="email"]');
        const passwordInput = signupForm.querySelector('input[name="password"]');
        const confirmInput = signupForm.querySelector('input[name="confirm_password"]');

        nameInput?.addEventListener('blur', function () {
            if (!this.value.trim()) {
                showFieldError(this, 'Name is required');
            } else if (this.value.trim().length < 2) {
                showFieldError(this, 'Name must be at least 2 characters');
            } else {
                clearFieldError(this);
            }
        });

        emailInput?.addEventListener('blur', function () {
            if (!this.value) {
                showFieldError(this, 'Email is required');
            } else if (!validateEmail(this.value)) {
                showFieldError(this, 'Please enter a valid email address');
            } else {
                clearFieldError(this);
            }
        });

        passwordInput?.addEventListener('blur', function () {
            if (!this.value) {
                showFieldError(this, 'Password is required');
            } else if (this.value.length < 6) {
                showFieldError(this, 'Password must be at least 6 characters');
            } else {
                clearFieldError(this);
            }
        });

        confirmInput?.addEventListener('blur', function () {
            if (!this.value) {
                showFieldError(this, 'Please confirm your password');
            } else if (this.value !== passwordInput.value) {
                showFieldError(this, 'Passwords do not match');
            } else {
                clearFieldError(this);
            }
        });

        signupForm.addEventListener('submit', function (e) {
            let isValid = true;

            if (!nameInput.value.trim() || nameInput.value.trim().length < 2) {
                showFieldError(nameInput, 'Please enter a valid name');
                isValid = false;
            }

            if (!emailInput.value || !validateEmail(emailInput.value)) {
                showFieldError(emailInput, 'Please enter a valid email address');
                isValid = false;
            }

            if (!passwordInput.value || passwordInput.value.length < 6) {
                showFieldError(passwordInput, 'Password must be at least 6 characters');
                isValid = false;
            }

            if (confirmInput.value !== passwordInput.value) {
                showFieldError(confirmInput, 'Passwords do not match');
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
            }
        });
    }
});

// Checkout Form Validation
document.addEventListener('DOMContentLoaded', function () {
    const checkoutForm = document.querySelector('form[action*="checkout"]');
    if (checkoutForm) {
        const phoneInput = checkoutForm.querySelector('input[name="phone"]');
        const zipInput = checkoutForm.querySelector('input[name="zip"]');

        // Phone Input - Real-time validation with visual feedback
        phoneInput?.addEventListener('input', function () {
            // Remove non-digits and limit to 10
            this.value = this.value.replace(/\D/g, '').slice(0, 10);

            // Visual feedback based on length
            if (this.value.length > 0 && this.value.length < 10) {
                this.classList.add('error');
                this.classList.remove('success');
                this.style.backgroundColor = '#FEE2E2'; // Light red
                showFieldError(this, `Phone number must be exactly 10 digits (${10 - this.value.length} more needed)`);
            } else if (this.value.length === 10) {
                this.classList.remove('error');
                this.classList.add('success');
                this.style.backgroundColor = '#D1FAE5'; // Light green
                clearFieldError(this);
            } else {
                this.classList.remove('error', 'success');
                this.style.backgroundColor = '';
                clearFieldError(this);
            }
        });

        phoneInput?.addEventListener('blur', function () {
            if (!this.value) {
                showFieldError(this, 'Phone number is required');
                this.classList.add('error');
                this.classList.remove('success');
                this.style.backgroundColor = '#FEE2E2';
            } else if (!validatePhone(this.value)) {
                showFieldError(this, 'Please enter a valid 10-digit phone number');
                this.classList.add('error');
                this.classList.remove('success');
                this.style.backgroundColor = '#FEE2E2';
            }
        });

        phoneInput?.addEventListener('focus', function () {
            if (this.value.length === 10) {
                this.style.backgroundColor = '#D1FAE5';
            }
        });

        // Zip Input - Real-time validation with visual feedback
        zipInput?.addEventListener('input', function () {
            // Remove non-digits and limit to 6
            this.value = this.value.replace(/\D/g, '').slice(0, 6);

            // Visual feedback based on length
            if (this.value.length > 0 && this.value.length < 6) {
                this.classList.add('error');
                this.classList.remove('success');
                this.style.backgroundColor = '#FEE2E2'; // Light red
                showFieldError(this, `Zip code must be exactly 6 digits (${6 - this.value.length} more needed)`);
            } else if (this.value.length === 6) {
                this.classList.remove('error');
                this.classList.add('success');
                this.style.backgroundColor = '#D1FAE5'; // Light green
                clearFieldError(this);
            } else {
                this.classList.remove('error', 'success');
                this.style.backgroundColor = '';
                clearFieldError(this);
            }
        });

        zipInput?.addEventListener('blur', function () {
            if (!this.value) {
                showFieldError(this, 'Zip code is required');
                this.classList.add('error');
                this.classList.remove('success');
                this.style.backgroundColor = '#FEE2E2';
            } else if (!validateZip(this.value)) {
                showFieldError(this, 'Please enter a valid 6-digit zip code');
                this.classList.add('error');
                this.classList.remove('success');
                this.style.backgroundColor = '#FEE2E2';
            }
        });

        zipInput?.addEventListener('focus', function () {
            if (this.value.length === 6) {
                this.style.backgroundColor = '#D1FAE5';
            }
        });

        // Form submission validation
        checkoutForm.addEventListener('submit', function (e) {
            let isValid = true;

            if (phoneInput && (!phoneInput.value || !validatePhone(phoneInput.value))) {
                showFieldError(phoneInput, 'Please enter a valid 10-digit phone number');
                phoneInput.classList.add('error');
                phoneInput.classList.remove('success');
                phoneInput.style.backgroundColor = '#FEE2E2';
                phoneInput.focus();
                isValid = false;
            }

            if (zipInput && (!zipInput.value || !validateZip(zipInput.value))) {
                showFieldError(zipInput, 'Please enter a valid 6-digit zip code');
                zipInput.classList.add('error');
                zipInput.classList.remove('success');
                zipInput.style.backgroundColor = '#FEE2E2';
                if (isValid) zipInput.focus();
                isValid = false;
            }

            if (!isValid) {
                e.preventDefault();
                showNotification('Please fix the form errors before submitting', 'error');
            }
        });
    }
});

// ============================================
// ENHANCED CART FUNCTIONALITY (NO RELOAD)
// ============================================

// Add to Cart
function addToCart(productId, event, quantity = 1) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }

    // Get quantity from input if on product detail page
    const quantityInput = document.getElementById('quantity');
    if (quantityInput) {
        quantity = parseInt(quantityInput.value) || 1;
    }

    const button = event?.target;
    if (button) {
        button.disabled = true;
        button.innerHTML = '<span class="spinner"></span> Adding...';
    }

    fetch('ajax_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=add&product_id=' + productId + '&quantity=' + quantity
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Product added to cart!', 'success');
                updateCartCount(data.cart_count);

                if (button) {
                    button.classList.add('added');
                    button.innerHTML = '✓ Added';
                    setTimeout(() => {
                        button.classList.remove('added');
                        button.innerHTML = 'Add to Cart';
                        button.disabled = false;
                    }, 2000);
                }
            } else {
                if (button) {
                    button.disabled = false;
                    button.innerHTML = 'Add to Cart';
                }
                showNotification(data.message || 'Error adding to cart', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (button) {
                button.disabled = false;
                button.innerHTML = 'Add to Cart';
            }
            showNotification('Error adding to cart', 'error');
        });
}

// Update Quantity (Live - No Reload)
function updateQuantity(productId, quantity) {
    if (quantity < 1) return;

    const cartItem = document.querySelector(`[data-product-id="${productId}"]`);
    const quantityInput = cartItem?.querySelector('.quantity-input');

    if (quantityInput) {
        quantityInput.disabled = true;
    }

    fetch('ajax_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=update&product_id=${productId}&quantity=${quantity}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update quantity input
                if (quantityInput) {
                    quantityInput.value = quantity;
                    quantityInput.disabled = false;
                }

                // Update item total
                const itemTotal = cartItem?.querySelector('.item-total');
                if (itemTotal && data.item_total) {
                    itemTotal.textContent = data.item_total;
                }

                // Update cart summary
                updateCartSummary(data);
                updateCartCount(data.cart_count);

                showNotification('Cart updated', 'success');
            } else {
                if (quantityInput) {
                    quantityInput.disabled = false;
                }
                showNotification(data.message || 'Error updating quantity', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (quantityInput) {
                quantityInput.disabled = false;
            }
            showNotification('Error updating quantity', 'error');
        });
}

// Helper function to increase cart quantity
function increaseCartQuantity(productId) {
    const quantityInput = document.getElementById(`qty-${productId}`);
    if (quantityInput) {
        const currentQty = parseInt(quantityInput.value) || 1;
        updateQuantity(productId, currentQty + 1);
    }
}

// Helper function to decrease cart quantity
function decreaseCartQuantity(productId) {
    const quantityInput = document.getElementById(`qty-${productId}`);
    if (quantityInput) {
        const currentQty = parseInt(quantityInput.value) || 1;
        if (currentQty > 1) {
            updateQuantity(productId, currentQty - 1);
        }
    }
}

// Remove from Cart (Live with Animation)
function removeFromCart(productId) {
    if (!confirm('Remove this item from cart?')) return;

    const cartItem = document.querySelector(`[data-product-id="${productId}"]`);
    if (cartItem) {
        cartItem.style.opacity = '0.5';
    }

    fetch('ajax_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=remove&product_id=${productId}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Animate removal
                if (cartItem) {
                    cartItem.style.transition = 'all 0.3s ease';
                    cartItem.style.transform = 'translateX(-100%)';
                    cartItem.style.opacity = '0';

                    setTimeout(() => {
                        cartItem.remove();

                        // Check if cart is empty
                        const remainingItems = document.querySelectorAll('.cart-item').length;
                        if (remainingItems === 0) {
                            location.reload(); // Show empty cart message
                        }
                    }, 300);
                }

                // Update cart summary
                updateCartSummary(data);
                updateCartCount(data.cart_count);

                showNotification('Item removed from cart', 'info');
            } else {
                if (cartItem) {
                    cartItem.style.opacity = '1';
                }
                showNotification(data.message || 'Error removing item', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            if (cartItem) {
                cartItem.style.opacity = '1';
            }
            showNotification('Error removing item', 'error');
        });
}

// Update Cart Summary
function updateCartSummary(data) {
    // Find all summary rows
    const summaryRows = document.querySelectorAll('.cart-summary .summary-row');

    if (data.subtotal && summaryRows[0]) {
        const subtotalEl = summaryRows[0].querySelector('span:last-child');
        if (subtotalEl) subtotalEl.textContent = data.subtotal;
    }

    if (data.shipping !== undefined && summaryRows[1]) {
        const shippingEl = summaryRows[1].querySelector('span:last-child');
        if (shippingEl) shippingEl.textContent = data.shipping;
    }

    if (data.tax && summaryRows[2]) {
        const taxEl = summaryRows[2].querySelector('span:last-child');
        if (taxEl) taxEl.textContent = data.tax;
    }

    if (data.total) {
        const totalEl = document.querySelector('.summary-total span:last-child');
        if (totalEl) totalEl.textContent = data.total;
    }

    // Update free shipping notice
    const freeShippingNotice = document.querySelector('.free-shipping-notice');
    if (freeShippingNotice && data.free_shipping_remaining) {
        if (data.free_shipping_remaining > 0) {
            freeShippingNotice.textContent = `Add ${data.free_shipping_remaining} more for FREE shipping!`;
            freeShippingNotice.style.display = 'block';
        } else {
            freeShippingNotice.style.display = 'none';
        }
    }
}

// Toggle Wishlist
function toggleWishlist(productId, event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }

    const button = event?.target.closest('.wishlist-btn');

    fetch('ajax_wishlist.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=toggle&product_id=' + productId
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (data.in_wishlist) {
                    button?.classList.add('active');
                    if (button) button.innerHTML = '❤️';
                    showNotification('Added to wishlist!', 'success');
                } else {
                    button?.classList.remove('active');
                    if (button) button.innerHTML = '🤍';
                    showNotification('Removed from wishlist', 'info');
                }
                updateWishlistCount(data.wishlist_count);
            } else {
                showNotification(data.message || 'Error updating wishlist', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error updating wishlist', 'error');
        });
}

// Update Cart Count
function updateCartCount(count) {
    const cartBadges = document.querySelectorAll('.cart-link .badge');
    cartBadges.forEach(badge => {
        if (count > 0) {
            badge.textContent = count;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    });
}

// Update Wishlist Count
function updateWishlistCount(count) {
    const wishlistBadges = document.querySelectorAll('.wishlist-link .badge');
    wishlistBadges.forEach(badge => {
        if (count > 0) {
            badge.textContent = count;
            badge.style.display = 'flex';
        } else {
            badge.style.display = 'none';
        }
    });
}

// Show Notification
function showNotification(message, type = 'info') {
    const existing = document.querySelector('.notification');
    if (existing) {
        existing.remove();
    }

    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;

    Object.assign(notification.style, {
        position: 'fixed',
        top: '100px',
        right: '20px',
        padding: '1rem 1.5rem',
        borderRadius: '12px',
        fontWeight: '500',
        zIndex: '10000',
        animation: 'slideInRight 0.3s ease-out',
        boxShadow: '0 4px 20px rgba(0, 0, 0, 0.15)',
        maxWidth: '300px'
    });

    if (type === 'success') {
        notification.style.background = '#D1FAE5';
        notification.style.color = '#065F46';
        notification.style.border = '1px solid #A7F3D0';
    } else if (type === 'error') {
        notification.style.background = '#FEE2E2';
        notification.style.color = '#991B1B';
        notification.style.border = '1px solid #FECACA';
    } else {
        notification.style.background = '#DBEAFE';
        notification.style.color = '#1E40AF';
        notification.style.border = '1px solid #BFDBFE';
    }

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Add animation keyframes
const style = document.createElement('style');
style.textContent = `
    @keyframes slideInRight {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }
    
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
    
    .spinner {
        display: inline-block;
        width: 14px;
        height: 14px;
        border: 2px solid rgba(255,255,255,0.3);
        border-radius: 50%;
        border-top-color: white;
        animation: spin 0.6s linear infinite;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
`;
document.head.appendChild(style);

// Prevent card click when clicking buttons
document.addEventListener('click', function (e) {
    if (e.target.closest('.wishlist-btn') || e.target.closest('.btn')) {
        e.stopPropagation();
    }
});
