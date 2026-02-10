<?php

namespace EasyCart\Controller;

use EasyCart\Services\AuthService;
use EasyCart\Services\OrderStatusService;
use EasyCart\Resource\Resource_Order;
use EasyCart\Collection\Collection_Category;
use EasyCart\View\View_Order_Index;
use EasyCart\View\View_Order_Success;
use EasyCart\View\View_Order_Detail;

/**
 * Controller_Order — Order Listing, Detail, Invoice, Archive
 * 
 * Uses Resource_Order instead of Legacy Repository.
 */
class Controller_Order extends Controller_Abstract
{
    private $categoryCollection;
    private $orderResource;

    public function __construct()
    {
        $this->categoryCollection = new Collection_Category();
        $this->orderResource = new Resource_Order();
    }

    /**
     * My orders page
     */
    public function index(): void
    {
        if (!AuthService::check()) {
            $this->redirect('/login');
        }

        $filter = $_GET['filter'] ?? 'active';
        $isArchived = ($filter === 'archived');
        $categories = $this->categoryCollection->getAll();
        $user_id = $_SESSION['user_id'];

        $dbOrders = $this->orderResource->findByUserId($user_id, $isArchived);
        $statusService = new OrderStatusService();

        $orders = [];
        foreach ($dbOrders as $o) {
            $resolvedStatus = $statusService::getStatus($o['status'], $o['created_at']);
            $orders[] = [
                'id' => $o['order_number'],
                'order_number' => $o['order_number'],
                'date' => date('F j, Y', strtotime($o['created_at'])),
                'subtotal' => $o['subtotal'],
                'shipping_cost' => $o['shipping_cost'],
                'shipping_method' => $o['shipping_method'] ?? 'Standard',
                'tax' => $o['tax'],
                'discount' => $o['discount'] ?? 0,
                'total' => $o['total'],
                'status_slug' => $resolvedStatus,
                'status_label' => $statusService::getLabel($resolvedStatus),
                'ship_to' => trim(($o['first_name'] ?? '') . ' ' . ($o['last_name'] ?? '')),
                'shipping_address' => trim(($o['address_line_one'] ?? '') . ', ' . ($o['city'] ?? '') . ', ' . ($o['state'] ?? '') . ' ' . ($o['postal_code'] ?? '')),
                'payment_method' => $o['payment_method'] ?? 'COD',
                'items' => $this->mapOrderItems($o['items'] ?? [])
            ];
        }

        $contentView = new View_Order_Index([
            'orders' => $orders,
            'filter' => $filter,
            'isArchived' => $isArchived,
            'categories' => $categories,
        ]);

        $this->renderWithLayout($contentView, [
            'page_title' => 'My Orders',
            'categories' => $categories,
        ]);
    }

    /**
     * Order success page
     */
    public function success(): void
    {
        $categories = $this->categoryCollection->getAll();
        $order_id = $_SESSION['last_order_id'] ?? null;

        $contentView = new View_Order_Success([
            'order_id' => $order_id,
            'categories' => $categories,
        ]);

        $this->renderWithLayout($contentView, [
            'page_title' => 'Order Placed Successfully',
            'categories' => $categories,
        ]);
    }

    /**
     * Order detail page
     */
    public function show($id): void
    {
        $categories = $this->categoryCollection->getAll();
        $order = $this->orderResource->findByOrderNumber($id);

        if ($order) {
            $statusService = new OrderStatusService();
            $resolvedStatus = $statusService::getStatus($order['status'], $order['created_at']);
            $order['status_slug'] = $resolvedStatus;
            $order['status_label'] = $statusService::getLabel($resolvedStatus);

            $order['items'] = $this->mapOrderItems($order['items'] ?? []);
        }

        $contentView = new View_Order_Detail([
            'order' => $order,
            'order_id' => $id,
            'categories' => $categories,
        ]);

        $this->renderWithLayout($contentView, [
            'page_title' => 'Order Details',
            'categories' => $categories,
        ]);
    }

    /**
     * Order invoice page (PDF-like HTML — no header/footer)
     */
    public function invoice($id): void
    {
        if (!AuthService::check()) {
            $this->redirect('/login');
        }

        $order = $this->orderResource->findByOrderNumber($id);

        if (!$order || $order['user_id'] != $_SESSION['user_id']) {
            $this->redirect('/orders');
        }

        $order['items'] = $this->mapOrderItems($order['items'] ?? [], true);

        // Invoice renders without header/footer layout
        $invoiceView = new View_Order_Detail([
            'order' => $order,
            'order_id' => $id,
        ], 'invoice');

        $invoiceView->render();
    }

    /**
     * Archive an order
     */
    public function archive($id): void
    {
        if (!AuthService::check()) {
            $this->jsonResponse(['success' => false, 'message' => 'Unauthorized']);
            return;
        }

        $order = $this->orderResource->findByOrderNumber($id);

        if (!$order || $order['user_id'] != $_SESSION['user_id']) {
            $this->jsonResponse(['success' => false, 'message' => 'Order not found']);
            return;
        }

        $status = isset($_GET['unarchive']) ? false : true;
        // User must use order_id (int) for archive, not order_number (string)
        $this->orderResource->archive($order['order_id'], $status);
        $this->jsonResponse(['success' => true]);
    }

    /**
     * Helper to map order items with image path normalization
     */
    private function mapOrderItems(array $items, bool $includeSku = false): array
    {
        return array_map(function ($item) use ($includeSku) {
            // Resource returns 'name', 'price'. Old DB cols 'product_name' etc?
            // Wait, Resource_Order::getItems selects 'op.*' from 'sales_order_product'.
            // sales_order_product cols: product_name, product_price, product_sku, quantity, product_entity_id.
            // But mapOrderItems expects keys: 'product_image', 'product_entity_id', 'product_name', 'product_price', 'quantity'.
            // does sales_order_product have 'product_image'? NO.
            // The Legacy `Queries::ORDER_GET_PRODUCTS` did a SUBQUERY to get image from `catalog_product_entity_media`.
            // My `Resource_Order::getItems` DOES NOT do this subquery! 
            // It just selects `op.*`.
            // So `item['product_image']` will be missing!

            // I need to update `Resource_Order::getItems` to fetch image too!
            // Or `Controller_Order::mapOrderItems` will have null image.

            // I must fix `Resource_Order` to fetch `product_image`.
            // QueryBuilder doesn't support subquery in select easily.
            // But I can JOIN `catalog_product_entity_media`?
            // "LEFT JOIN catalog_product_entity_media m ON op.product_entity_id = m.entity_id AND m.type = 'image'"?
            // But media table structure? simple join might work.
            // Let's assume I fix `Resource_Order` next. I will define Controller assuming `product_image` exists.

            $image = $item['product_image'] ?? null;
            if ($image && strpos($image, '/assets/') !== 0 && strpos($image, 'assets/') !== 0) {
                $image = '/assets/images/products/' . $image;
            } elseif ($image && strpos($image, 'assets/') === 0) {
                $image = '/' . $image;
            }

            $mapped = [
                'product_id' => $item['product_entity_id'] ?? 0,
                'name' => $item['product_name'],
                'price' => $item['product_price'],
                'quantity' => $item['quantity'],
                'image' => $image,
                'total' => $item['product_price'] * $item['quantity'],
                'url_key' => $item['url_key'] ?? null
            ];

            if ($includeSku) {
                $mapped['sku'] = $item['product_sku'] ?? 'N/A'; // Resource returns product_sku
            }

            return $mapped;
        }, $items);
    }
}
