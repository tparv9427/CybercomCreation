<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=DM+Sans:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/skeleton.css">
    <!-- EasyCart v<?php echo EASYCART_VERSION; ?> -->
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <div class="logo">
                <a href="index.php"><?php echo SITE_NAME; ?></a>
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
                <a href="index.php">Home</a>
                <a href="products.php">Shop</a>
                <div class="dropdown">
                    <a href="#" class="dropdown-toggle">Categories ▾</a>
                    <div class="dropdown-menu">
                        <?php foreach ($categories as $category): ?>
                            <a href="products.php?category=<?php echo $category['id']; ?>"><?php echo $category['name']; ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="dropdown">
                    <a href="#" class="dropdown-toggle">Brands ▾</a>
                    <div class="dropdown-menu">
                        <?php $brands = getBrands(); ?>
                        <?php foreach ($brands as $brand): ?>
                            <a href="brand.php?id=<?php echo $brand['id']; ?>"><?php echo $brand['name']; ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <a href="cart.php" class="cart-link">
                    Cart 
                    <?php if (getCartCount() > 0): ?>
                        <span class="badge"><?php echo getCartCount(); ?></span>
                    <?php endif; ?>
                </a>
                <a href="wishlist.php" class="wishlist-link">
                    Wishlist
                    <?php if (getWishlistCount() > 0): ?>
                        <span class="badge"><?php echo getWishlistCount(); ?></span>
                    <?php endif; ?>
                </a>
                <?php if (isLoggedIn()): ?>
                    <a href="orders.php">Orders</a>
                    <a href="logout.php" onclick="confirmLogout(event)">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                <?php endif; ?>
                <button class="theme-toggle" id="themeToggle" aria-label="Toggle dark mode">
                    <span class="sun-icon">☀️</span>
                    <span class="moon-icon">🌙</span>
                </button>
            </nav>
        </div>
    </header>
