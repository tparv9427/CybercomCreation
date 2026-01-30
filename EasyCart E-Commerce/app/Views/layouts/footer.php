<!-- Footer -->
<footer class="footer">
    <div class="footer-container">
        <div class="footer-grid">
            <div class="footer-section">
                <h3><?php echo SITE_NAME; ?></h3>
                <p>Your trusted online marketplace for quality products at great prices.</p>
            </div>
            <div class="footer-section">
                <h4>Quick Links</h4>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="products.php">Shop</a></li>
                    <li><a href="cart.php">Cart</a></li>
                    <li><a href="wishlist.php">Wishlist</a></li>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Categories</h4>
                <ul>
                    <?php
                    $footerCategories = array_slice($categories, 0, 4);
                    foreach ($footerCategories as $category):
                        ?>
                        <li><a
                                href="products.php?category=<?php echo $category['id']; ?>"><?php echo $category['name']; ?></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <div class="footer-section">
                <h4>Customer Service</h4>
                <ul>
                    <li><a href="#">Contact Us</a></li>
                    <li><a href="#">FAQ</a></li>
                    <li><a href="#">Shipping Info</a></li>
                    <li><a href="#">Returns</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved. |
                v<?php echo EASYCART_VERSION; ?></p>
        </div>
    </div>
</footer>

<!-- Generic Confirmation Modal -->
<?php include __DIR__ . '/../partials/confirmation_modal.php'; ?>

<script src="/assets/js/skeleton.js?v=<?php echo time(); ?>"></script>
<script src="/assets/js/pagination.js?v=<?php echo time(); ?>"></script>
<script src="/assets/js/main.js?v=<?php echo time(); ?>"></script>
</body>

</html>