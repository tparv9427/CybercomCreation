<?php
if (!isLoggedIn()) {
    header('Location: login.php');
    exit;
}
$page_title = 'My Orders';
?>
<link rel="stylesheet" href="assets/css/orders.css">
<div class="breadcrumb"><a href="index.php">Home</a> / My Orders</div>
<div class="container">
    <div class="section-header">
        <h2 class="section-title">My Orders</h2>
        <p class="section-subtitle">Track your purchases</p>
    </div>
    <div class="orders-list">
        <?php if (empty($orders)): ?>
            <div class="empty-state">
                <p>You haven't placed any orders yet.</p>
                <a href="index.php" class="btn">Start Shopping</a>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="order-card">
                    <div class="order-header">
                        <div>
                            <h4>Order #<?= htmlspecialchars($order['id']) ?></h4>
                            <p>Placed on <?= htmlspecialchars($order['date']) ?></p>
                        </div>
                        <span
                            class="order-status <?= strtolower($order['status']) ?>"><?= htmlspecialchars($order['status']) ?></span>
                    </div>
                    <div class="order-items">
                        <?php foreach ($order['items'] as $item): ?>
                            <div class="order-item">
                                <div class="order-item-image">
                                    <?php if (isset($item['image']) && $item['image']): ?>
                                        <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                                    <?php else: ?>
                                        📦
                                    <?php endif; ?>
                                </div>
                                <div class="order-item-details">
                                    <h5><?= htmlspecialchars($item['name']) ?></h5>
                                    <p>Quantity: <?= $item['quantity'] ?></p>
                                </div>
                                <div class="order-item-price"><?= \EasyCart\Helpers\FormatHelper::price($item['price']) ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="order-footer">
                        <div class="order-total">Total: <?= \EasyCart\Helpers\FormatHelper::price($order['total']) ?></div>
                        <!-- <button class="btn btn-outline">View Details</button> -->
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>