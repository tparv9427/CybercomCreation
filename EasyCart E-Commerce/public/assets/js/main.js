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
        slide.style.transition = 'transform 0.8s ease-in-out';

        if (index === 0) {
            slide.style.transform = 'translateX(0)';
        } else {
            slide.style.transform = 'translateX(100%)';
        }
    });

    function goToSlide(index) {
        if (index === currentSlide) return;

        const currentSlideEl = slides[currentSlide];
        const nextSlideEl = slides[index];
        const isNext = index > currentSlide || (currentSlide === slideCount - 1 && index === 0);

        // Reset next slide to starting position
        nextSlideEl.style.transition = 'none';
        nextSlideEl.style.transform = isNext ? 'translateX(100%)' : 'translateX(-100%)';

        // Force reflow
        void nextSlideEl.offsetWidth;

        // Restore transition
        nextSlideEl.style.transition = 'transform 0.8s ease-in-out';
        currentSlideEl.style.transition = 'transform 0.8s ease-in-out';

        // Animate
        currentSlideEl.style.transform = isNext ? 'translateX(-100%)' : 'translateX(100%)';
        nextSlideEl.style.transform = 'translateX(0)';

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
    // Get quantity from input if on product detail page
    const quantityInput = document.getElementById('quantity') || document.getElementById('qty-select');
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

                // Check for shipping category change
                if (data.shipping_category && data.shipping_category !== currentShippingCategory) {
                    const oldCategory = currentShippingCategory;
                    currentShippingCategory = data.shipping_category;

                    if (oldCategory !== null) {
                        const categoryName = data.shipping_category === 'freight' ? 'Freight' : 'Express';
                        showNotification(
                            `Shipping options changed to ${categoryName} category`,
                            'info'
                        );
                    }
                } else if (data.shipping_category) {
                    // Initialize category if not set
                    currentShippingCategory = data.shipping_category;
                }

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
function updateQuantity(productId, quantity, disableInput = true) {
    if (quantity < 1) return;

    const cartItem = document.querySelector(`[data-product-id="${productId}"]`);
    const quantityInput = cartItem?.querySelector('.quantity-input');

    if (quantityInput && disableInput) {
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
                // Update quantity input with server-enforced value if different
                if (quantityInput) {
                    // If server returned a different quantity (e.g. capped by stock), update input
                    // Use loose comparison or parsing because one might be string
                    if (data.actual_quantity !== undefined && parseInt(data.actual_quantity) !== parseInt(quantity)) {
                        quantityInput.value = data.actual_quantity;
                        quantity = data.actual_quantity; // Update local variable for downstream logic
                        showNotification(`Quantity adjusted to available stock (${data.actual_quantity})`, 'info');
                    } else {
                        quantityInput.value = quantity;
                    }
                    quantityInput.setAttribute('data-old-value', quantity); // Store valid value
                    quantityInput.disabled = false;
                    // Keep focus if we were typing
                    if (!disableInput) {
                        quantityInput.focus();
                    }
                }

                // Update decrease button state (Minus vs Delete)
                const decreaseBtn = document.getElementById(`btn-decrease-${productId}`);
                if (decreaseBtn) {
                    if (quantity == 1) {
                        decreaseBtn.textContent = '🗑';
                        decreaseBtn.classList.add('delete-btn-sm');
                        decreaseBtn.setAttribute('onclick', `removeFromCart(${productId})`);
                    } else {
                        decreaseBtn.textContent = '−'; // minus symbol
                        decreaseBtn.classList.remove('delete-btn-sm');
                        decreaseBtn.setAttribute('onclick', `decreaseCartQuantity(${productId})`);
                    }
                }

                // Update item total
                const itemTotal = cartItem?.querySelector('.item-total');
                if (itemTotal && data.item_total) {
                    itemTotal.textContent = data.item_total;
                }

                // Update cart summary
                updateCartSummary(data);
                updateCartCount(data.cart_count);

                // Check for shipping category change
                if (data.shipping_category && data.shipping_category !== currentShippingCategory) {
                    const oldCategory = currentShippingCategory;
                    currentShippingCategory = data.shipping_category;

                    if (oldCategory !== null) {
                        const categoryName = data.shipping_category === 'freight' ? 'Freight' : 'Express';
                        showNotification(
                            `Shipping options changed to ${categoryName} category`,
                            'info'
                        );
                    }
                } else if (data.shipping_category) {
                    currentShippingCategory = data.shipping_category;
                }

                // Show discreet success indicator for manual entry
                if (!disableInput) {
                    quantityInput.classList.add('updated-flash');
                    setTimeout(() => quantityInput.classList.remove('updated-flash'), 500);
                } else {
                    showNotification('Cart updated', 'success');
                }

                // Trigger pricing update if on checkout (though usually this function is for cart page)
                // But if we are on checkout execution context, we might need it.
                // However, cart page implementation is separate.
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
        const maxQty = parseInt(quantityInput.max) || 999;

        if (currentQty < maxQty) {
            updateQuantity(productId, currentQty + 1);
        } else {
            showNotification(`Sorry, only ${maxQty} items available in stock!`, 'error');
            // Shake animation for feedback
            quantityInput.parentElement.classList.add('shake');
            setTimeout(() => quantityInput.parentElement.classList.remove('shake'), 500);
        }
    }
}

// Validate manual input with Debounce
let debounceTimer;
function validateCartQuantity(productId, input) {
    clearTimeout(debounceTimer);
    const max = parseInt(input.max) || 999;
    let value = parseInt(input.value);

    // Immediate check for max stock (prevent typing huge numbers)
    if (!isNaN(value) && value > max) {
        input.value = max;
        value = max;
        showNotification(`Sorry, only ${max} items available in stock!`, 'error');

        // Shake animation
        const container = input.parentElement;
        container.classList.add('shake');
        setTimeout(() => container.classList.remove('shake'), 500);
    }

    debounceTimer = setTimeout(() => {
        let currentValue = parseInt(input.value);

        // Final validation (check for empty/min value)
        if (input.value === '' || isNaN(currentValue) || currentValue < 1) {
            const previousValue = input.getAttribute('data-old-value') || 1;
            input.value = previousValue;
            showNotification('Please enter a valid quantity', 'error');

            // Shake animation
            const container = input.parentElement;
            container.classList.add('shake');
            setTimeout(() => container.classList.remove('shake'), 500);
            return;
        }

        // Always update with the (possibly corrected) value
        updateQuantity(productId, currentValue, false);
    }, 300); // 300ms debounce
}

// Generic Max Stock Validator
function validateMaxStock(input) {
    let value = parseInt(input.value);
    const max = parseInt(input.max) || 999;

    // Retrieve previous valid value
    const previousValue = input.getAttribute('data-old-value') || 1;

    if (isNaN(value) || value < 1) {
        // Revert to previous value
        input.value = previousValue;
        showNotification('Invalid cart value', 'error');

        // Shake animation
        const container = input.parentElement;
        container.classList.add('shake');
        setTimeout(() => container.classList.remove('shake'), 500);

        return false;
    } else if (value > max) {
        input.value = max;
        showNotification(`Sorry, only ${max} items available in stock!`, 'error');

        // Shake animation
        const container = input.parentElement;
        container.classList.add('shake');
        setTimeout(() => container.classList.remove('shake'), 500);

        return false;
    }
    return true;
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
    showConfirmationModal({
        title: 'Remove Item',
        message: 'Are you sure you want to remove this item from your cart?',
        confirmText: 'Remove',
        onConfirm: function () {
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

                        // Check for shipping category change
                        if (data.shipping_category && data.shipping_category !== currentShippingCategory) {
                            const oldCategory = currentShippingCategory;
                            currentShippingCategory = data.shipping_category;

                            if (oldCategory !== null) {
                                const categoryName = data.shipping_category === 'freight' ? 'Freight' : 'Express';
                                showNotification(
                                    `Shipping options changed to ${categoryName} category`,
                                    'info'
                                );
                            }
                        } else if (data.shipping_category) {
                            currentShippingCategory = data.shipping_category;
                        }

                        // Check if cart is now empty and reload to show empty state
                        if (data.cart_count === 0) {
                            // Reload the page to show empty cart state
                            setTimeout(() => {
                                window.location.reload();
                            }, 500); // Small delay to show the notification
                            return;
                        }

                        showNotification('Item removed from cart', 'success');
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
    });
}

// Update Cart Summary
// Update Cart Summary
function updateCartSummary(data) {
    // Update Subtotal
    const subtotalEl = document.getElementById('summary-subtotal');
    if (subtotalEl && data.subtotal) {
        subtotalEl.textContent = data.subtotal;
    }

    // Update Tax (Item Tax)
    const taxEl = document.getElementById('summary-tax');
    if (taxEl) {
        // Use item_tax if available (tax on items only), otherwise fallback to standard tax
        taxEl.textContent = data.item_tax || data.tax;
    }

    // Update Cart Value (Subtotal + Tax)
    const cartValueEl = document.getElementById('summary-cart-value');
    if (cartValueEl && data.cart_value) {
        cartValueEl.textContent = data.cart_value;
    }

    // Update Delivery Type
    const deliveryTypeEl = document.getElementById('summary-delivery-type');
    if (deliveryTypeEl && data.shipping_category) {
        const categoryName = data.shipping_category === 'freight' ? 'Freight' : 'Express';
        deliveryTypeEl.textContent = `${categoryName} Shipping`;
    }

    // Update Estimated Total Range
    const estimatedTotalEl = document.getElementById('summary-estimated-total');
    if (estimatedTotalEl && data.estimated_total_min && data.estimated_total_max) {
        estimatedTotalEl.textContent = `${data.estimated_total_min} - ${data.estimated_total_max}`;
    }
}

// Update Header Cart Count
function updateCartCount(count) {
    // Update all cart badges (desktop + mobile if any)
    const cartLinks = document.querySelectorAll('.cart-link');
    cartLinks.forEach(link => {
        let badge = link.querySelector('.badge');
        if (count > 0) {
            if (!badge) {
                badge = document.createElement('span');
                badge.className = 'badge';
                link.appendChild(badge);
            }
            badge.textContent = count;
            badge.style.display = 'inline-flex';
        } else {
            if (badge) badge.remove();
        }
    });
}

// Update Header Wishlist Count
function updateWishlistCount(count) {
    const wishlistLinks = document.querySelectorAll('.wishlist-link');
    wishlistLinks.forEach(link => {
        let badge = link.querySelector('.badge');
        if (count > 0) {
            if (!badge) {
                badge = document.createElement('span');
                badge.className = 'badge';
                link.appendChild(badge);
            }
            badge.textContent = count;
            badge.style.display = 'inline-flex';
        } else {
            if (badge) badge.remove();
        }
    });
}

// Fetch Initial Counts
function fetchInitialCounts() {
    // Fetch Cart Count
    fetch('ajax_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=count'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateCartCount(data.cart_count);
            }
        })
        .catch(console.error);

    // Fetch Wishlist Count
    fetch('ajax_wishlist.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=count'
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateWishlistCount(data.wishlist_count);
            }
        })
        .catch(console.error);
}

// Call on load
document.addEventListener('DOMContentLoaded', fetchInitialCounts);

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
                    showNotification('Removed from wishlist', 'success');
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

// Update Cart Count - Removed duplicate definition
// Logic consolidated above

// Move to Cart (Wishlist)
function moveToCart(productId, event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }

    const button = event?.target;
    if (button) {
        button.disabled = true;
        button.innerHTML = '<span class="spinner"></span> Moving...';
    }

    // Find the wishlist item card to remove it later
    const wishlistItem = button?.closest('.product-card'); // Assuming it's in a grid card

    fetch('ajax_wishlist.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=move&product_id=' + productId
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('Moved to cart!', 'success');
                updateCartCount(data.cart_count);
                updateWishlistCount(data.wishlist_count);

                // Animate removal from wishlist view
                if (wishlistItem) {
                    wishlistItem.style.transition = 'all 0.3s ease';
                    wishlistItem.style.transform = 'scale(0.8)';
                    wishlistItem.style.opacity = '0';
                    setTimeout(() => {
                        wishlistItem.remove();
                        // Check if wishlist is empty and show message if needed
                        const remaining = document.querySelectorAll('.product-grid .product-card').length;
                        if (remaining === 0) {
                            location.reload();
                        }
                    }, 300);
                }
            } else {
                showNotification(data.message || 'Error moving to cart', 'error');
                if (button) {
                    button.disabled = false;
                    button.innerHTML = 'Move to Cart';
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error moving to cart', 'error');
            if (button) {
                button.disabled = false;
                button.innerHTML = 'Move to Cart';
            }
        });
}

// Update Wishlist Count - Removed duplicate definition
// Logic consolidated above

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
    } else if (type === 'saved') {
        notification.style.background = '#F3E8FF';
        notification.style.color = '#6B21A8';
        notification.style.border = '1px solid #D8B4FE';
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

// ============================================
// SHIPPING CATEGORY TRACKING
// ============================================
let currentShippingCategory = null; // Track for toast notifications

// ============================================
// CHECKOUT PRICING UPDATE (PHASE 4)
// ============================================
function updateCheckoutPricing() {
    // Updated to work with radio buttons
    const shippingRadios = document.querySelectorAll('input[name="shipping"]');
    const paymentSelect = document.getElementById('payment-select');

    let shippingMethod = 'standard';
    shippingRadios.forEach(radio => {
        if (radio.checked) {
            shippingMethod = radio.value;
        }
    });

    const paymentMethod = paymentSelect ? paymentSelect.value : 'card';

    fetch('ajax_checkout_pricing.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'shipping=' + shippingMethod + '&payment=' + paymentMethod
    })
        .then(response => {
            if (!response.ok) {
                console.error('Response not OK:', response.status);
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            console.log('Pricing update response:', data);
            if (data.debug) {
                console.log('🛒 Cart Debug Info:', {
                    isBuyNow: data.debug.is_buynow,
                    itemCount: data.debug.cart_items,
                    cartData: data.debug.cart_data
                });
            }
            if (data.success) {
                // Update Summary DOM
                const summaryTotals = document.querySelector('.summary-totals');
                const shippingEl = document.querySelector('.summary-totals .summary-row:nth-child(2) span:last-child');
                const taxEl = document.querySelector('.summary-totals .summary-row:nth-child(3) span:last-child'); // This selector might be fragile if rows change
                const totalEl = document.querySelector('.summary-totals .summary-total span:last-child');
                const btnTotalEl = document.querySelector('.btn-place-order');

                if (shippingEl) shippingEl.textContent = data.pricing.shipping;

                // Handle Payment Fee Row
                let feeRow = document.getElementById('payment-fee-row');

                if (data.pricing.payment_fee) {
                    if (!feeRow) {
                        feeRow = document.createElement('div');
                        feeRow.id = 'payment-fee-row';
                        feeRow.className = 'summary-row';
                        feeRow.innerHTML = `<span>COD Fee:</span><span>${data.pricing.payment_fee}</span>`;

                        // Insert before tax row (assuming tax row is last before total)
                        const totalRow = document.querySelector('.summary-total');
                        if (totalRow) {
                            totalRow.parentNode.insertBefore(feeRow, totalRow);
                        }
                    } else {
                        feeRow.querySelector('span:last-child').textContent = data.pricing.payment_fee;
                    }
                } else {
                    if (feeRow) {
                        feeRow.remove();
                    }
                }

                // We need to re-query tax el because row injection might shift indices if we used nth-child
                // So let's try to find tax row by content text if possible, or just rely on class structure if we add classes
                // For now, let's assume the tax row is the one before the total row, excluding our new fee row
                // Refined selector strategy:
                const allRows = document.querySelectorAll('.summary-totals .summary-row');
                allRows.forEach(row => {
                    if (row.textContent.includes('Tax')) {
                        row.querySelector('span:last-child').textContent = data.pricing.tax;
                    }
                });

                if (totalEl) totalEl.textContent = data.pricing.total;
                if (btnTotalEl) btnTotalEl.textContent = 'Place Order - ' + data.pricing.total;
            }
        })
        .catch(error => {
            console.error('Error updating pricing:', error);
        });
}

// Add radio button event listeners
document.addEventListener('DOMContentLoaded', function () {
    const shippingRadios = document.querySelectorAll('input[name="shipping"]');
    shippingRadios.forEach(radio => {
        radio.addEventListener('change', updateCheckoutPricing);
    });
});


// Save item for later
function saveForLater(productId, event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }

    fetch('ajax_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=save_for_later&product_id=${productId}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove from cart list if present
                const cartItem = document.querySelector(`.cart-item[data-product-id="${productId}"]`);
                if (cartItem) {
                    cartItem.remove();
                }

                // Update Summaries
                if (typeof updateCartSummary === 'function' && document.querySelector('.summary-totals')) {
                    updateCartSummary(data);
                }

                // Update Cart Count (always present in header ideally)
                updateCartCount(data.cart_count);

                // Add to Saved List - Only if section exists (Cart Page)
                let savedSection = document.querySelector('.saved-items-section');
                if (savedSection || cartItem) { // Only try to manage saved section if we are on cart page (inferred by presence of cartItem or section)
                    if (data.saved_item_html) {
                        if (!savedSection) {
                            // Create section if it doesn't exist
                            savedSection = document.createElement('div');
                            savedSection.className = 'saved-items-section';
                            savedSection.style.marginTop = '3rem';
                            savedSection.innerHTML = `
                        <h3 style="margin-bottom: 1.5rem; color: var(--primary);">Saved for Later</h3>
                        <div class="cart-items"></div>
                    `;
                            document.querySelector('.container').appendChild(savedSection);
                        }

                        // Append item
                        const savedItemsList = savedSection.querySelector('.cart-items');
                        savedItemsList.insertAdjacentHTML('beforeend', data.saved_item_html);

                        // Update count in header
                        const countHeader = savedSection.querySelector('h3');
                        if (countHeader) {
                            const count = savedItemsList.children.length;
                            countHeader.textContent = `Saved for Later (${count})`;
                        }
                    }

                    // Check if cart is empty
                    const remaining = document.querySelectorAll('.cart-layout .cart-item');
                    if (remaining.length === 0) {
                        location.reload(); // Reload to show empty state
                    }

                    // showNotification(data.message || 'Item saved for later', 'success'); // Optional: suppressing notification for instant feel or keep it? User didn't specify. Keeping it.
                    showNotification(data.message || 'Item saved for later', 'saved');
                } else {
                    showNotification(data.message || 'Error saving item', 'error');
                }
            } else {
                showNotification(data.message || 'Error saving item', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error processing request', 'error');
        });
}

// Move item to cart from saved
function moveToCartFromSaved(productId) {
    fetch('ajax_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: `action=move_to_cart&product_id=${productId}`
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove from saved list
                const savedItem = document.getElementById(`saved-item-${productId}`);
                if (savedItem) {
                    savedItem.remove();
                }
                // Update Saved Count or Remove Section
                const savedSection = document.querySelector('.saved-items-section');
                if (savedSection) {
                    const savedItemsList = savedSection.querySelector('.cart-items');
                    if (savedItemsList.children.length === 0) {
                        savedSection.remove();
                    } else {
                        const countHeader = savedSection.querySelector('h3');
                        if (countHeader) {
                            countHeader.textContent = `Saved for Later (${savedItemsList.children.length})`;
                        }
                    }
                }

                // Update Summaries
                updateCartSummary(data);
                updateCartCount(data.cart_count);

                // Add to Cart List
                if (data.cart_item_html) {
                    const cartItemsList = document.querySelector('.cart-layout .cart-items');
                    if (cartItemsList) {
                        cartItemsList.insertAdjacentHTML('beforeend', data.cart_item_html);
                    } else {
                        // Cart was empty, simplest to reload to restore layout
                        location.reload();
                        return;
                    }
                }

                showNotification(data.message || 'Item moved to cart', 'success');
            } else {
                showNotification(data.message || 'Error moving item', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error processing request', 'error');
        });
}

// Attach listener to shipping and payment select
document.addEventListener('DOMContentLoaded', function () {
    const shippingSelect = document.getElementById('shipping-select');
    const paymentSelect = document.getElementById('payment-select');

    if (shippingSelect) {
        shippingSelect.addEventListener('change', updateCheckoutPricing);
    }

    if (paymentSelect) {
        paymentSelect.addEventListener('change', updateCheckoutPricing);
    }
});
