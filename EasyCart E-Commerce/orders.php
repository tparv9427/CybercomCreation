<?php
require_once 'includes/config.php';
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}
$page_title = 'My Orders';
include 'includes/header.php';
?>
<link rel="stylesheet" href="assets/css/orders.css">
<div class="breadcrumb"><a href="index.php">Home</a> / My Orders</div>
<div class="container">
    <div class="section-header">
        <h2 class="section-title">My Orders</h2>
        <p class="section-subtitle">Track your purchases</p>
    </div>
    <div class="orders-list">
        <div class="order-card">
            <div class="order-header">
                <div>
                    <h4>Order #ORD-A1B2C3D4</h4>
                    <p>Placed on January 15, 2026</p>
                </div>
                <span class="order-status delivered">Delivered</span>
            </div>
            <div class="order-items">
                <div class="order-item">
                    <div class="order-item-image">📱</div>
                    <div class="order-item-details">
                        <h5>Awesome Gadget Pro Max</h5>
                        <p>Quantity: 1</p>
                    </div>
                    <div class="order-item-price">$99.99</div>
                </div>
            </div>
            <div class="order-footer">
                <div class="order-total">Total: $119.99</div>
                <button class="btn btn-outline">View Details</button>
            </div>
        </div>
        <div class="order-card">
            <div class="order-header">
                <div>
                    <h4>Order #ORD-E5F6G7H8</h4>
                    <p>Placed on December 28, 2025</p>
                </div>
                <span class="order-status processing">Processing</span>
            </div>
            <div class="order-items">
                <div class="order-item">
                    <div class="order-item-image">👟</div>
                    <div class="order-item-details">
                        <h5>Cool Sneaker Elite</h5>
                        <p>Quantity: 2</p>
                    </div>
                    <div class="order-item-price">$99.98</div>
                </div>
            </div>
            <div class="order-footer">
                <div class="order-total">Total: $117.98</div>
                <button class="btn btn-outline">Track Order</button>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
