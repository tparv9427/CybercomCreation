<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_token() ?>">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <link
        href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=DM+Sans:wght@400;500;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/skeleton.css">
    <!-- EasyCart v<?php echo EASYCART_VERSION; ?> -->
</head>

<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <div class="logo">
                <a href="/"><?php echo SITE_NAME; ?></a>
            </div>

            <!-- Search Bar -->
            <div class="search-container">
                <input type="text" class="search-bar" id="searchInput" placeholder="Search products...">
                <button class="search-btn" onclick="performSearch()">🔍</button>
            </div>

            <!-- Mobile Menu Toggle -->
            <button class="mobile-menu-toggle" id="mobileMenuToggle" aria-label="Toggle menu">
                <span></span>
                <span></span>
                <span></span>
            </button>

            <!-- Navigation -->
            <nav class="nav" id="mainNav">
                <a href="/">Home</a>
                <a href="/products">Shop</a>
                <div class="dropdown">
                    <a href="#" class="dropdown-toggle">Categories ▾</a>
                    <div class="dropdown-menu">
                        <?php foreach ($categories as $category): ?>
                            <a href="/products?category=<?php echo $category['id']; ?>"><?php echo $category['name']; ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="dropdown">
                    <a href="#" class="dropdown-toggle">Brands ▾</a>
                    <div class="dropdown-menu">
                        <?php $brands = getBrands(); ?>
                        <?php foreach ($brands as $brand): ?>
                            <a href="/brand/<?php echo $brand['id']; ?>"><?php echo $brand['name']; ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <a href="/cart" class="cart-link">
                    Cart
                    <?php if (getCartCount() > 0): ?>
                        <span class="badge"><?php echo getCartCount(); ?></span>
                    <?php endif; ?>
                </a>
                <?php if (isLoggedIn()): ?>
                    <div class="dropdown account-dropdown">
                        <a href="#" class="dropdown-toggle" style="display: flex; align-items: center; gap: 0.5rem;">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            <?php echo strtoupper($_SESSION['user_name']); ?> ▾
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                            <a href="/dashboard">
                                <div class="dropdown-item-content">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="3" y="3" width="7" height="7"></rect>
                                        <rect x="14" y="3" width="7" height="7"></rect>
                                        <rect x="14" y="14" width="7" height="7"></rect>
                                        <rect x="3" y="14" width="7" height="7"></rect>
                                    </svg>
                                    Dashboard
                                </div>
                            </a>
                            <a href="/wishlist">
                                <div class="dropdown-item-content">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path
                                            d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z">
                                        </path>
                                    </svg>
                                    Wishlist
                                    <?php if (getWishlistCount() > 0): ?>
                                        <span class="inline-badge"><?php echo getWishlistCount(); ?></span>
                                    <?php endif; ?>
                                </div>
                            </a>
                            <a href="/orders">
                                <div class="dropdown-item-content">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path
                                            d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z">
                                        </path>
                                        <path d="m3.3 7 8.7 5 8.7-5"></path>
                                        <path d="M12 22V12"></path>
                                    </svg>
                                    My Orders
                                </div>
                            </a>
                            <a href="/admin/import-export">
                                <div class="dropdown-item-content">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                                        <polyline points="17 8 12 3 7 8"></polyline>
                                        <line x1="12" y1="3" x2="12" y2="15"></line>
                                    </svg>
                                    Import / Export
                                </div>
                            </a>
                            <hr style="margin: 0.5rem 0; border: 0; border-top: 1px solid var(--border);">
                            <a href="/logout" onclick="confirmLogout(event)" style="color: var(--accent);">
                                <div class="dropdown-item-content">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                        <polyline points="16 17 21 12 16 7"></polyline>
                                        <line x1="21" y1="12" x2="9" y2="12"></line>
                                    </svg>
                                    Logout
                                </div>
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="/wishlist" class="wishlist-link">
                        Wishlist
                        <?php if (getWishlistCount() > 0): ?>
                            <span class="badge"><?php echo getWishlistCount(); ?></span>
                        <?php endif; ?>
                    </a>
                    <a href="/login">Login</a>
                <?php endif; ?>
                <button class="theme-toggle" id="themeToggle" aria-label="Toggle dark mode">
                    <span class="sun-icon">☀️</span>
                    <span class="moon-icon">🌙</span>
                </button>
            </nav>
        </div>
    </header>