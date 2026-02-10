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

    <div class="order-filters">
        <a href="/orders?filter=active"
            class="filter-tab <?= ($filter === 'active' || !$filter) ? 'active' : '' ?>">Orders</a>
        <a href="/orders?filter=archived"
            class="filter-tab <?= ($filter === 'archived') ? 'active' : '' ?>">Archived</a>
    </div>

    <script>
        async function toggleArchive(orderNumber, status = true) {
            try {
                const url = `/order/archive/${orderNumber}${status ? '' : '?unarchive=1'}`;
                const response = await fetch(url);
                const data = await response.json();

                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Error occurred');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Something went wrong');
            }
        }
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.order-list-container').forEach(container => {
                container.addEventListener('click', (e) => {
                    const card = e.target.closest('.order-card');
                    if (!card) return;

                    // If clicking a button or an active link, don't redirect the main card
                    if (e.target.closest('button') || e.target.closest('a')) {
                        return;
                    }
                    const orderId = card.getAttribute('data-order-id');
                    if (orderId) {
                        window.location.href = `/order/${orderId}`;
                    }
                });
            });
        });
    </script>
    <div class="orders-list order-list-container">
        <?php if (empty($orders)): ?>
            <div class="empty-state">
                <div class="empty-icon">📂</div>
                <p><?= $isArchived ? "You don't have any archived orders." : "You haven't placed any orders yet." ?></p>
                <?php if (!$isArchived): ?>
                    <a href="/products" class="btn">Start Shopping</a>
                <?php else: ?>
                    <a href="/orders?filter=active" class="btn">View Active Orders</a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <?php foreach ($orders as $order): ?>
                <div class="order-card" data-order-id="<?= $order['id'] ?>">
                    <!-- Always Visible: Main Info Row -->
                    <div class="order-header">
                        <div class="order-main-info">
                            <div class="info-group">
                                <span class="info-label">Order Placed</span>
                                <span class="info-value"><?= htmlspecialchars($order['date']) ?></span>
                            </div>
                            <div class="info-group">
                                <span class="info-label">Total</span>
                                <span class="info-value"><?= \EasyCart\Helpers\FormatHelper::price($order['total']) ?></span>
                            </div>
                            <div class="info-group">
                                <span class="info-label">Ship To</span>
                                <span class="info-value" title="<?= htmlspecialchars($order['shipping_address']) ?>"
                                    style="color: var(--primary); display: flex; align-items: center; gap: 0.25rem; cursor: pointer;">
                                    <?= htmlspecialchars($order['ship_to'] ?: $_SESSION['user_name']) ?> ▾
                                </span>
                            </div>
                            <div class="info-group" style="text-align: right;">
                                <span class="info-label">Order # <?= htmlspecialchars($order['id']) ?></span>
                                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 4px;">
                                    <a href="/order/invoice/<?= $order['id'] ?>" target="_blank"
                                        style="font-size: 0.75rem; color: var(--secondary); text-decoration: underline;">Invoice
                                        ▾</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Always Visible: Status Banner -->
                    <div class="order-status-banner">
                        <h4>
                            <?= $order['status_slug'] === 'delivered' ? 'Delivered' : 'Arriving soon' ?>
                        </h4>
                        <div class="status-row-info">
                            <span class="order-status <?= strtolower($order['status_slug']) ?>">
                                <?= htmlspecialchars($order['status_label']) ?>
                            </span>
                            <span class="status-message">
                                Your package is currently <?= strtolower($order['status_label']) ?> and will reach you soon.
                            </span>
                        </div>
                    </div>

                    <!-- Hidden by default: Details Drawer (Visible on Hover) -->
                    <div class="order-details-drawer">
                        <div class="order-details-grid">
                            <!-- Left: Items -->
                            <div class="order-items">
                                <?php foreach ($order['items'] as $item): ?>
                                    <div class="order-item">
                                        <div class="order-item-image">
                                            <?php if ($item['image']): ?>
                                                <img src="<?= htmlspecialchars($item['image']) ?>"
                                                    alt="<?= htmlspecialchars($item['name']) ?>">
                                            <?php else: ?>
                                                <span style="font-size: 2rem;">📦</span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="order-item-details">
                                            <a href="<?= product_url($item) ?>"
                                                style="text-decoration: none; color: #2563eb; font-weight: 600; font-size: 1.05rem;">
                                                <?= htmlspecialchars($item['name']) ?>
                                            </a>
                                            <p style="margin: 0.75rem 0; color: var(--secondary);">
                                                Quantity: <?= $item['quantity'] ?> × <span
                                                    style="font-weight: 600; color: var(--primary);"><?= \EasyCart\Helpers\FormatHelper::price($item['price']) ?></span>
                                            </p>
                                            <div style="display: flex; gap: 1rem;">
                                                <button class="action-btn"
                                                    onclick="window.location.href='/checkout?action=buynow&id=<?= $item['product_id'] ?>&quantity=1'"
                                                    style="width: auto; padding: 0.5rem 1.25rem; font-size: 0.8rem; background: #fbbf24; border: none; color: #000;">
                                                    Buy it again
                                                </button>
                                                <button class="action-btn"
                                                    onclick="window.location.href='<?= product_url($item) ?>'"
                                                    style="width: auto; padding: 0.5rem 1.25rem; font-size: 0.8rem;">
                                                    View item
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Right: Actions Sidebar -->
                            <div class="order-actions-sidebar">
                                <button class="action-btn primary">Track package</button>
                                <button class="action-btn">Return or replace items</button>
                                <button class="action-btn">Write a product review</button>
                                <?php if ($isArchived): ?>
                                    <button class="action-btn"
                                        onclick="toggleArchive('<?= htmlspecialchars($order['order_number'] ?? $order['id']) ?>', false)">Unarchive
                                        order</button>
                                <?php else: ?>
                                    <button class="action-btn"
                                        onclick="toggleArchive('<?= htmlspecialchars($order['order_number'] ?? $order['id']) ?>', true)">Archive
                                        order</button>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="order-summary-breakup"
                            style="margin-top: 2rem; border-top: 1px solid var(--border); padding-top: 1.5rem; background: transparent;">
                            <div class="summary-row">
                                <span>Subtotal</span>
                                <span><?= \EasyCart\Helpers\FormatHelper::price($order['subtotal']) ?></span>
                            </div>
                            <div class="summary-row">
                                <span>Shipping (<?= htmlspecialchars($order['shipping_method']) ?>)</span>
                                <span><?= \EasyCart\Helpers\FormatHelper::price($order['shipping_cost']) ?></span>
                            </div>
                            <div class="summary-row">
                                <span>Tax</span>
                                <span><?= \EasyCart\Helpers\FormatHelper::price($order['tax']) ?></span>
                            </div>
                            <?php if (($order['discount'] ?? 0) > 0): ?>
                                <div class="summary-row" style="color: #059669;">
                                    <span>Discount Applied</span>
                                    <span>-<?= \EasyCart\Helpers\FormatHelper::price($order['discount']) ?></span>
                                </div>
                            <?php endif; ?>
                            <div class="summary-row"
                                style="font-weight: 800; color: var(--primary); margin-top: 0.75rem; font-size: 1.2rem; border-top: 1px dashed var(--border); padding-top: 0.75rem;">
                                <span>Grand Total</span>
                                <span><?= \EasyCart\Helpers\FormatHelper::price($order['total']) ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>