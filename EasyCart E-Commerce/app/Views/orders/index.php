<?php
if (!isLoggedIn()) {
    header('Location: /login');
    exit;
}
$page_title = 'My Orders';
?>
<link rel="stylesheet" href="/assets/css/orders.css">
<div class="breadcrumb"><a href="/">Home</a> / My Orders</div>
<div class="container">
    <div class="section-header">
        <h2 class="section-title">My Orders</h2>
        <p class="section-subtitle">Track your purchases</p>
    </div>
    <div class="orders-list">
        <?php if (empty($orders)): ?>
            <div class="empty-state">
                <div class="empty-icon">📂</div>
                <p>You haven't placed any orders yet.</p>
                <a href="/products" class="btn">Start Shopping</a>
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

                    <div class="order-summary-breakup">
                        <div class="summary-row">
                            <span>Subtotal</span>
                            <span><?= \EasyCart\Helpers\FormatHelper::price($order['subtotal']) ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Shipping Cost</span>
                            <span><?= \EasyCart\Helpers\FormatHelper::price($order['shipping_cost']) ?></span>
                        </div>
                        <div class="summary-row">
                            <span>Tax</span>
                            <span><?= \EasyCart\Helpers\FormatHelper::price($order['tax']) ?></span>
                        </div>
                        <?php if ($order['discount'] > 0): ?>
                            <div class="summary-row" style="color: #059669;">
                                <span>Discount</span>
                                <span>-<?= \EasyCart\Helpers\FormatHelper::price($order['discount']) ?></span>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="order-footer">
                        <div>
                            <div class="order-total">Total: <?= \EasyCart\Helpers\FormatHelper::price($order['total']) ?></div>
                            <div class="payment-shipping-info">
                                <span class="info-badge">
                                    🚚 <?= htmlspecialchars($order['shipping_method']) ?>
                                </span>
                                <span class="info-badge">
                                    💳 <?= htmlspecialchars($order['payment_method']) ?>
                                </span>
                            </div>
                        </div>
                        <!-- <button class="btn btn-outline">Track Order</button> -->
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>