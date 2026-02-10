<?php
/**
 * Order Detail Template
 * Path: app/View/Templates/orders/detail.php
 */

$statusSlug = strtolower($order['status_slug'] ?? $order['status'] ?? '');
$statusColor = 'gray';

if (in_array($statusSlug, ['completed', 'delivered'])) {
    $statusColor = 'green';
} elseif (in_array($statusSlug, ['proccessing', 'processing'])) {
    $statusColor = 'blue';
} elseif ($statusSlug === 'cancelled') {
    $statusColor = 'red';
}
?>
<style>
    .order-detail-container {
        max-width: 1000px;
        margin: 0 auto;
        padding: 20px;
        font-family: 'Inter', system-ui, sans-serif;
    }

    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        border-bottom: 1px solid #eee;
        padding-bottom: 20px;
    }

    .page-title h1 {
        margin: 0;
        font-size: 24px;
        color: #111;
    }

    .page-title span {
        font-size: 14px;
        color: #666;
    }

    .status-badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 600;
        text-transform: capitalize;
    }

    .status-green {
        background: #d1fae5;
        color: #065f46;
    }

    .status-blue {
        background: #dbeafe;
        color: #1e40af;
    }

    .status-red {
        background: #fee2e2;
        color: #991b1b;
    }

    .status-gray {
        background: #f3f4f6;
        color: #374151;
    }

    .order-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 30px;
    }

    .card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 20px;
    }

    .card-header {
        background: #f9fafb;
        padding: 15px 20px;
        border-bottom: 1px solid #e5e7eb;
        font-weight: 600;
        color: #374151;
    }

    .card-body {
        padding: 20px;
    }

    .order-item {
        display: flex;
        gap: 15px;
        padding-bottom: 15px;
        margin-bottom: 15px;
        border-bottom: 1px solid #f3f4f6;
    }

    .order-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .item-image {
        width: 80px;
        height: 80px;
        background: #f3f4f6;
        border-radius: 6px;
        object-fit: cover;
    }

    .item-info {
        flex: 1;
    }

    .item-name {
        font-weight: 600;
        color: #111;
        margin-bottom: 5px;
        display: block;
        text-decoration: none;
    }

    .item-meta {
        font-size: 13px;
        color: #666;
    }

    .item-price {
        font-weight: 600;
        color: #111;
    }

    .address-box p {
        margin: 0;
        line-height: 1.6;
        color: #4b5563;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 10px;
        font-size: 14px;
        color: #4b5563;
    }

    .summary-total {
        border-top: 1px solid #e5e7eb;
        padding-top: 15px;
        margin-top: 15px;
        font-weight: 700;
        font-size: 18px;
        color: #111;
    }

    .btn {
        display: inline-block;
        padding: 10px 20px;
        background: #2563eb;
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-weight: 500;
        font-size: 14px;
        transition: background 0.2s;
        border: none;
        cursor: pointer;
    }

    .btn:hover {
        background: #1d4ed8;
    }

    .btn-outline {
        background: white;
        border: 1px solid #d1d5db;
        color: #374151;
    }

    .btn-outline:hover {
        background: #f9fafb;
    }

    @media (max-width: 768px) {
        .order-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="order-detail-container">
    <div style="margin-bottom: 15px;">
        <a href="/orders" style="color: #666; text-decoration: none;">&larr; Back to Orders</a>
    </div>

    <div class="page-header">
        <div class="page-title">
            <h1>Order #
                <?= htmlspecialchars($order['order_number']) ?>
            </h1>
            <span>Placed on
                <?= date('F j, Y g:i A', strtotime($order['created_at'])) ?>
            </span>
        </div>
        <div>
            <span class="status-badge status-<?= $statusColor ?>">
                <?= htmlspecialchars($order['status_label'] ?? $order['status']) ?>
            </span>
        </div>
    </div>

    <div class="order-grid">
        <!-- Left Column: Items -->
        <div class="main-content">
            <div class="card">
                <div class="card-header">Items Ordered</div>
                <div class="card-body">
                    <?php foreach ($order['items'] as $item): ?>
                        <div class="order-item">
                            <img src="<?= htmlspecialchars($item['image'] ?? '/assets/images/placeholder.png') ?>"
                                class="item-image" alt="Product Image">
                            <div class="item-info">
                                <a href="<?= product_url($item) ?>" class="item-name">
                                    <?= htmlspecialchars($item['name']) ?>
                                </a>
                                <div class="item-meta">
                                    SKU:
                                    <?= htmlspecialchars($item['sku'] ?? 'N/A') ?> <br>
                                    Qty:
                                    <?= $item['quantity'] ?>
                                </div>
                            </div>
                            <div class="item-price">
                                <?= \EasyCart\Helpers\FormatHelper::price($item['price']) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="card">
                <div class="card-header">Payment & Shipping Method</div>
                <div class="card-body" style="display: flex; gap: 40px;">
                    <div>
                        <div style="font-size: 12px; color: #666; text-transform: uppercase; margin-bottom: 5px;">
                            Payment</div>
                        <strong>
                            <?= htmlspecialchars($order['payment_method'] ?? 'COD') ?>
                        </strong>
                    </div>
                    <div>
                        <div style="font-size: 12px; color: #666; text-transform: uppercase; margin-bottom: 5px;">
                            Shipping</div>
                        <strong>
                            <?= htmlspecialchars($order['shipping_method'] ?? 'Standard Delivery') ?>
                        </strong>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Sidebar -->
        <div class="sidebar">
            <!-- Actions -->
            <div style="margin-bottom: 20px; display: flex; flex-direction: column; gap: 10px;">
                <a href="/order/invoice/<?= $order['order_number'] ?>" target="_blank" class="btn btn-outline"
                    style="text-align: center;">
                    Print Invoice
                </a>
                <button class="btn" onclick="alert('Tracking feature coming soon!')">Track Package</button>
            </div>

            <!-- Summary -->
            <div class="card">
                <div class="card-header">Order Summary</div>
                <div class="card-body">
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>
                            <?= \EasyCart\Helpers\FormatHelper::price($order['subtotal']) ?>
                        </span>
                    </div>
                    <div class="summary-row">
                        <span>Shipping</span>
                        <span>
                            <?= \EasyCart\Helpers\FormatHelper::price($order['shipping_cost']) ?>
                        </span>
                    </div>
                    <div class="summary-row">
                        <span>Tax</span>
                        <span>
                            <?= \EasyCart\Helpers\FormatHelper::price($order['tax']) ?>
                        </span>
                    </div>
                    <?php if (($order['discount'] ?? 0) > 0): ?>
                        <div class="summary-row" style="color: green;">
                            <span>Discount</span>
                            <span>-
                                <?= \EasyCart\Helpers\FormatHelper::price($order['discount']) ?>
                            </span>
                        </div>
                    <?php endif; ?>
                    <div class="summary-row summary-total">
                        <span>Total</span>
                        <span>
                            <?= \EasyCart\Helpers\FormatHelper::price($order['total']) ?>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Address -->
            <div class="card">
                <div class="card-header">Shipping Address</div>
                <div class="card-body address-box">
                    <strong>
                        <?= htmlspecialchars($order['ship_first'] . ' ' . $order['ship_last']) ?>
                    </strong><br>
                    <?= htmlspecialchars($order['ship_address']) ?><br>
                    <?= htmlspecialchars($order['ship_city']) ?>,
                    <?= htmlspecialchars($order['ship_state']) ?>
                    <?= htmlspecialchars($order['ship_zip']) ?><br>
                    <?= htmlspecialchars($order['ship_country']) ?><br>
                    Phone:
                    <?= htmlspecialchars($order['ship_phone']) ?>
                </div>
            </div>
        </div>
    </div>
</div>