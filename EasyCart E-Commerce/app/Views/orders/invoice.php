<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $page_title ?>
    </title>
    <style>
        :root {
            --primary-color: #232f3e;
            --text-color: #333;
            --border-color: #ddd;
            --header-bg: #f8f8f8;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.5;
            color: var(--text-color);
            margin: 0;
            padding: 20px;
            background: #fff;
        }

        .invoice-container {
            max-width: 900px;
            margin: 0 auto;
            border: 1px solid var(--border-color);
            padding: 30px;
            position: relative;
        }

        .invoice-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 30px;
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 15px;
        }

        .company-logo {
            font-size: 28px;
            font-weight: 800;
            color: var(--primary-color);
            letter-spacing: -1px;
        }

        .company-logo span {
            color: #ff9900;
        }

        .invoice-title {
            text-align: right;
        }

        .invoice-title h1 {
            margin: 0;
            font-size: 24px;
            text-transform: uppercase;
        }

        .invoice-meta {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .meta-group {
            flex: 1;
        }

        .meta-group h3 {
            font-size: 14px;
            text-transform: uppercase;
            color: #666;
            margin-bottom: 8px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 3px;
        }

        .address-box {
            font-size: 13px;
            line-height: 1.4;
        }

        .invoice-details {
            margin-bottom: 30px;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            background: var(--header-bg);
            padding: 15px;
            border-radius: 4px;
        }

        .detail-item {
            font-size: 14px;
        }

        .detail-item strong {
            display: block;
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th {
            background: var(--header-bg);
            text-align: left;
            padding: 10px;
            font-size: 12px;
            text-transform: uppercase;
            border-bottom: 2px solid var(--border-color);
        }

        td {
            padding: 12px 10px;
            border-bottom: 1px solid var(--border-color);
            font-size: 14px;
        }

        .item-desc {
            max-width: 400px;
        }

        .text-right {
            text-align: right;
        }

        .totals-section {
            display: flex;
            justify-content: flex-end;
        }

        .totals-table {
            width: 300px;
        }

        .totals-table td {
            border: none;
            padding: 5px 10px;
        }

        .grand-total {
            font-size: 18px;
            font-weight: 700;
            color: var(--primary-color);
            border-top: 2px solid var(--primary-color) !important;
            margin-top: 10px;
        }

        .footer {
            margin-top: 50px;
            font-size: 12px;
            color: #777;
            text-align: center;
            border-top: 1px solid var(--border-color);
            padding-top: 20px;
        }

        .no-print {
            margin-bottom: 20px;
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            border: none;
        }

        .btn-print {
            background: #ff9900;
            color: #000;
        }

        .btn-back {
            background: #eee;
            color: #333;
        }

        @media print {
            .no-print {
                display: none;
            }

            body {
                padding: 0;
            }

            .invoice-container {
                border: none;
                margin: 0;
                width: 100%;
                max-width: none;
            }
        }
    </style>
</head>

<body>

    <div class="no-print">
        <a href="/orders" class="btn btn-back">← Back to Orders</a>
        <button onclick="window.print()" class="btn btn-print">Print Invoice (PDF)</button>
    </div>

    <div class="invoice-container">
        <div class="invoice-header">
            <div class="company-logo">EASY<span>CART</span></div>
            <div class="invoice-title">
                <h1>Tax Invoice</h1>
                <p style="font-size: 12px; color: #666;">Original for Recipient</p>
            </div>
        </div>

        <div class="invoice-meta">
            <div class="meta-group" style="margin-right: 40px;">
                <h3>Sold By</h3>
                <div class="address-box">
                    <strong>EasyCart Retail Limited</strong><br>
                    Plot 42, Tech Park, Sector 5<br>
                    Gandhinagar, Gujarat, 382010<br>
                    GSTIN: 24AAABC1234A1Z1
                </div>
            </div>
            <div class="meta-group">
                <h3>Shipping Address</h3>
                <div class="address-box">
                    <strong>
                        <?= htmlspecialchars($order['ship_first'] . ' ' . $order['ship_last']) ?>
                    </strong><br>
                    <?= htmlspecialchars($order['ship_address']) ?><br>
                    <?= htmlspecialchars($order['ship_city']) ?>,
                    <?= htmlspecialchars($order['ship_state']) ?>,
                    <?= htmlspecialchars($order['ship_zip']) ?><br>
                    <?= htmlspecialchars($order['ship_country']) ?><br>
                    Phone:
                    <?= htmlspecialchars($order['ship_phone']) ?>
                </div>
            </div>
        </div>

        <div class="invoice-details">
            <div class="detail-item">
                <strong>Order Number</strong>
                <?= htmlspecialchars($order['order_number']) ?>
            </div>
            <div class="detail-item">
                <strong>Order Date</strong>
                <?= date('d.m.Y', strtotime($order['created_at'])) ?>
            </div>
            <div class="detail-item">
                <strong>Invoice Number</strong>
                INV-
                <?= date('Y') ?>-
                <?= str_pad($order['order_id'], 6, '0', STR_PAD_LEFT) ?>
            </div>
            <div class="detail-item">
                <strong>Payment Method</strong>
                <?= htmlspecialchars($order['payment_method'] ?: 'COD') ?>
            </div>
        </div>

        <table>
            <thead>
                <tr>
                    <th style="width: 50px;">Sl. No</th>
                    <th>Description</th>
                    <th class="text-right">Unit Price</th>
                    <th class="text-right" style="width: 50px;">Qty</th>
                    <th class="text-right">Net Amount</th>
                    <th class="text-right">Tax (18%)</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $i = 1;
                foreach ($order['items'] as $item):
                    $net = $item['price'] / 1.18;
                    $tax = $item['price'] - $net;
                    ?>
                    <tr>
                        <td>
                            <?= $i++ ?>
                        </td>
                        <td class="item-desc">
                            <strong>
                                <?= htmlspecialchars($item['name']) ?>
                            </strong><br>
                            <span style="font-size: 11px; color: #666;">SKU:
                                <?= htmlspecialchars($item['sku'] ?? 'N/A') ?>
                            </span>
                        </td>
                        <td class="text-right">
                            <?= \EasyCart\Helpers\FormatHelper::price($net) ?>
                        </td>
                        <td class="text-right">
                            <?= $item['quantity'] ?>
                        </td>
                        <td class="text-right">
                            <?= \EasyCart\Helpers\FormatHelper::price($net * $item['quantity']) ?>
                        </td>
                        <td class="text-right">
                            <?= \EasyCart\Helpers\FormatHelper::price($tax * $item['quantity']) ?>
                        </td>
                        <td class="text-right">
                            <?= \EasyCart\Helpers\FormatHelper::price($item['price'] * $item['quantity']) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="totals-section">
            <table class="totals-table">
                <tr>
                    <td>Subtotal:</td>
                    <td class="text-right">
                        <?= \EasyCart\Helpers\FormatHelper::price($order['subtotal']) ?>
                    </td>
                </tr>
                <tr>
                    <td>Shipping (
                        <?= htmlspecialchars($order['shipping_method'] ?: 'Standard') ?>):
                    </td>
                    <td class="text-right">
                        <?= \EasyCart\Helpers\FormatHelper::price($order['shipping_cost']) ?>
                    </td>
                </tr>
                <tr>
                    <td>Tax Collected:</td>
                    <td class="text-right">
                        <?= \EasyCart\Helpers\FormatHelper::price($order['tax']) ?>
                    </td>
                </tr>
                <?php if ($order['discount'] > 0): ?>
                    <tr>
                        <td>Discount:</td>
                        <td class="text-right">-
                            <?= \EasyCart\Helpers\FormatHelper::price($order['discount']) ?>
                        </td>
                    </tr>
                <?php endif; ?>
                <tr class="grand-total">
                    <td>Grand Total:</td>
                    <td class="text-right">
                        <?= \EasyCart\Helpers\FormatHelper::price($order['total']) ?>
                    </td>
                </tr>
            </table>
        </div>

        <div style="margin-top: 40px; border: 1px solid var(--border-color); padding: 15px; font-size: 13px;">
            <strong>Amount in Words:</strong><br>
            <span style="text-transform: capitalize;">
                <?= \EasyCart\Helpers\FormatHelper::numberToWords($order['total']) ?> Only
            </span>
        </div>

        <div class="footer">
            <p>This is a computer generated invoice and does not require a physical signature.</p>
            <p>EasyCart E-Commerce | www.easycart.com | support@easycart.com</p>
        </div>
    </div>

</body>

</html>