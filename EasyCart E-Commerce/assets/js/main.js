// Dark Mode Toggle
const themeToggle = document.getElementById('themeToggle');
const html = document.documentElement;

// Check for saved theme preference or default to 'light'
const currentTheme = localStorage.getItem('theme') || 'light';
html.setAttribute('data-theme', currentTheme);

themeToggle?.addEventListener('click', function() {
    const theme = html.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
    html.setAttribute('data-theme', theme);
    localStorage.setItem('theme', theme);
});

// Mobile Menu Toggle
const mobileMenuToggle = document.getElementById('mobileMenuToggle');
const mainNav = document.getElementById('mainNav');

mobileMenuToggle?.addEventListener('click', function() {
    this.classList.toggle('active');
    mainNav.classList.toggle('active');
});

// Close mobile menu when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.header-container')) {
        mobileMenuToggle?.classList.remove('active');
        mainNav?.classList.remove('active');
    }
});

// Search Functionality
function performSearch() {
    const searchInput = document.getElementById('searchInput');
    const query = searchInput.value.trim();
    
    if (query) {
        window.location.href = 'search.php?q=' + encodeURIComponent(query);
    }
}

// Search on Enter key
document.getElementById('searchInput')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        performSearch();
    }
});

// View Toggle (Grid/Row)
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

// Restore saved view preference
const savedView = localStorage.getItem('view');
if (savedView && savedView === 'row') {
    toggleView('row');
}

// Add to Cart
function addToCart(productId, event) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }
    
    fetch('ajax_cart.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'action=add&product_id=' + productId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Product added to cart!', 'success');
            updateCartCount(data.cart_count);
            
            // Update button state
            const button = event?.target;
            if (button) {
                button.classList.add('added');
                button.textContent = '✓ Added';
                setTimeout(() => {
                    button.classList.remove('added');
                    button.textContent = 'Add to Cart';
                }, 2000);
            }
        } else {
            showNotification(data.message || 'Error adding to cart', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error adding to cart', 'error');
    });
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
                button.innerHTML = '❤️';
                showNotification('Added to wishlist!', 'success');
            } else {
                button?.classList.remove('active');
                button.innerHTML = '🤍';
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
    // Remove existing notification
    const existing = document.querySelector('.notification');
    if (existing) {
        existing.remove();
    }
    
    // Create notification
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    
    // Add styles
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
    
    // Remove after 3 seconds
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
`;
document.head.appendChild(style);

// Update Quantity
function updateQuantity(productId, quantity) {
    if (quantity < 1) return;
    
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
            location.reload(); // Reload to update totals
        } else {
            showNotification(data.message || 'Error updating quantity', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Error updating quantity', 'error');
    });
}

// Remove from Cart
function removeFromCart(productId) {
    if (confirm('Remove this item from cart?')) {
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
                location.reload();
            } else {
                showNotification(data.message || 'Error removing item', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error removing item', 'error');
        });
    }
}

// Toggle Order Details (Accordion)
function toggleOrderDetails(orderId) {
    const details = document.getElementById('order-details-' + orderId);
    const button = event.target;
    
    if (details.style.display === 'none' || !details.style.display) {
        details.style.display = 'block';
        button.textContent = 'Hide Details ▲';
    } else {
        details.style.display = 'none';
        button.textContent = 'View Details ▼';
    }
}

// Prevent card click when clicking buttons
document.addEventListener('click', function(e) {
    if (e.target.closest('.wishlist-btn') || e.target.closest('.btn')) {
        e.stopPropagation();
    }
});
