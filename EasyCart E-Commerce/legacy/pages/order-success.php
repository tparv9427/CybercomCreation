<?php
require_once 'includes/config.php';
$page_title = 'Order Placed';
$order_id = $_SESSION['last_order_id'] ?? 'ORD-UNKNOWN';
include 'includes/header.php';
?>
<div class="container" style="text-align: center; padding: 6rem 2rem;">
    <div style="font-size: 5rem; margin-bottom: 2rem;">✅</div>
    <h2 style="font-family: 'Cormorant Garamond', serif; font-size: 2.5rem; margin-bottom: 1rem; color: var(--primary);">Order Placed Successfully!</h2>
    <p style="font-size: 1.2rem; color: var(--text); margin-bottom: 2rem;">Your order ID is: <strong><?php echo $order_id; ?></strong></p>
    <p style="color: var(--secondary); margin-bottom: 3rem;">We'll send you a confirmation email shortly.</p>
    <div style="display: flex; gap: 1rem; justify-content: center;">
        <a href="orders.php" class="btn btn-primary">View Orders</a>
        <a href="products.php" class="btn btn-outline">Continue Shopping</a>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
