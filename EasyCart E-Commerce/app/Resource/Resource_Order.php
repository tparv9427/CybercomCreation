<?php

namespace EasyCart\Resource;

use EasyCart\Database\QueryBuilder;
use EasyCart\Core\Database;

/**
 * Resource_Order — Order DB Configuration
 * 
 * Table: sales_order
 * Primary Key: order_id
 */
class Resource_Order extends Resource_Abstract
{
    protected $table = 'sales_order';
    protected $primaryKey = 'order_id';
    protected $columns = [
        'order_id',
        'order_number',
        'user_id',
        'original_cart_id',
        'status',
        'subtotal',
        'shipping_cost',
        'tax',
        'discount',
        'total',
        'is_archived',
        'created_at',
        'updated_at'
    ];

    /**
     * Find orders by user ID
     */
    public function findByUserId(int $userId, bool $archived = false): array
    {
        $qb = QueryBuilder::select($this->table . ' o', [
            'o.*',
            'op.shipping_method',
            'op.payment_method',
            'oa.first_name',
            'oa.last_name',
            'oa.address_line_one',
            'oa.city',
            'oa.state',
            'oa.postal_code'
        ])
            ->leftJoin('sales_order_payment op', 'o.order_id = op.order_id')
            ->leftJoin('sales_order_address oa', "o.order_id = oa.order_id AND oa.address_type = 'shipping'")
            ->where('o.user_id', '=', $userId)
            ->where('o.is_archived', '=', $archived)
            ->orderBy('o.created_at', 'DESC');

        $orders = $qb->fetchAll();

        foreach ($orders as &$order) {
            $order['items'] = $this->getItems($order['order_id']);
        }

        return $orders;
    }

    /**
     * Get order items
     */
    public function getItems(int $orderId): array
    {
        return QueryBuilder::select('sales_order_product op', [
            'op.*',
            '(SELECT image_path FROM catalog_product_image WHERE product_entity_id = op.product_entity_id AND is_primary = true LIMIT 1) as product_image',
            '(SELECT url_key FROM catalog_product_entity WHERE entity_id = op.product_entity_id LIMIT 1) as url_key'
        ])
            ->where('op.order_id', '=', $orderId)
            ->fetchAll();
    }

    /**
     * Find by Order Number
     */
    public function findByOrderNumber(string $orderNumber)
    {
        $order = QueryBuilder::select($this->table . ' o', ['o.*'])
            ->where('o.order_number', '=', $orderNumber)
            ->fetchOne();

        if ($order) {
            $order['items'] = $this->getItems($order['order_id']);

            $address = QueryBuilder::select('sales_order_address', ['*'])
                ->where('order_id', '=', $order['order_id'])
                ->where('address_type', '=', 'shipping')
                ->fetchOne();

            if ($address) {
                $order['ship_first'] = $address['first_name'];
                $order['ship_last'] = $address['last_name'];
                $order['ship_address'] = $address['address_line_one'];
                $order['ship_city'] = $address['city'];
                $order['ship_state'] = $address['state'];
                $order['ship_zip'] = $address['postal_code'];
                $order['ship_country'] = $address['country'];
                $order['ship_phone'] = $address['phone'];

                $order = array_merge($order, $address);
            }
            // Add payment info too?
            $payment = QueryBuilder::select('sales_order_payment', ['*'])
                ->where('order_id', '=', $order['order_id'])
                ->fetchOne();
            if ($payment) {
                $order['payment_method'] = $payment['payment_method'];
                $order['shipping_method'] = $payment['shipping_method'];
            }
        }
        return $order;
    }

    /**
     * Archive order
     */
    public function archive(int $orderId, bool $status): void
    {
        QueryBuilder::update($this->table, ['is_archived' => $status])
            ->where('order_id', '=', $orderId)
            ->execute();
    }

    /**
     * Get Dashboard Stats
     */
    public function getDashboardStats(int $userId): array
    {
        return QueryBuilder::select($this->table, [
            'COUNT(order_id) as total_orders',
            'COALESCE(SUM(total), 0) as total_spent'
        ])
            ->where('user_id', '=', $userId)
            ->where('status', '!=', 'cancelled')
            ->fetchOne();
    }

    /**
     * Get Chart Data
     */
    public function getChartData(int $userId): array
    {
        return QueryBuilder::select($this->table, [
            'DATE(created_at) as order_date',
            'SUM(total) as daily_total',
            'COUNT(order_id) as order_count'
        ])
            ->where('user_id', '=', $userId)
            ->where('status', '!=', 'cancelled')
            ->groupBy('DATE(created_at)')
            ->orderBy('order_date', 'ASC')
            ->fetchAll();
    }

    /**
     * Create generic order (replaces legacy save)
     */
    public function createOrder(array $orderData, array $items): int
    {
        $pdo = Database::getInstance()->getConnection();

        try {
            $pdo->beginTransaction();

            $orderNumber = $orderData['order_number'];

            $stmt = $pdo->prepare("
                INSERT INTO sales_order 
                (order_number, user_id, original_cart_id, subtotal, shipping_cost, tax, discount, total, status, created_at)
                VALUES 
                (:order_no, :uid, :cid, :sub, :ship, :tax, :disc, :total, :status, NOW())
                RETURNING order_id
            ");

            $stmt->execute([
                ':order_no' => $orderNumber,
                ':uid' => $orderData['user_id'],
                ':cid' => $orderData['original_cart_id'] ?? null,
                ':sub' => $orderData['subtotal'],
                ':ship' => $orderData['shipping_cost'],
                ':tax' => $orderData['tax'],
                ':disc' => $orderData['discount'] ?? 0,
                ':total' => $orderData['total'],
                ':status' => $orderData['status'] ?? 'Processing'
            ]);

            $orderId = $stmt->fetchColumn();

            // Insert Items
            $stmt = $pdo->prepare("
                INSERT INTO sales_order_product (order_id, product_entity_id, quantity, product_name, product_price, product_sku, row_total, created_at)
                VALUES (:oid, :pid, :qty, :name, :price, :sku, :total, NOW())
            ");

            foreach ($items as $item) {
                $rowTotal = (float) $item['price'] * (int) $item['quantity'];
                $stmt->execute([
                    ':oid' => $orderId,
                    ':pid' => $item['id'], // item['id'] comes from product['id'] as mapped in Controller
                    ':qty' => $item['quantity'],
                    ':name' => $item['name'],
                    ':price' => $item['price'],
                    ':sku' => $item['sku'] ?? 'N/A',
                    ':total' => $rowTotal
                ]);
            }

            $pdo->commit();
            return (int) $orderId;

        } catch (\Exception $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    /**
     * Add address to order
     */
    public function addAddress(int $orderId, string $type, array $data): bool
    {
        $pdo = Database::getInstance()->getConnection();

        $parts = explode(' ', $data['name'] ?? '', 2);
        $firstName = $parts[0] ?? '';
        $lastName = $parts[1] ?? '';

        $stmt = $pdo->prepare("
            INSERT INTO sales_order_address 
            (order_id, address_type, first_name, last_name, email, phone, address_line_one, city, state, postal_code, country)
            VALUES 
            (:oid, :type, :fname, :lname, :email, :phone, :addr, :city, :state, :zip, :country)
        ");

        return $stmt->execute([
            ':oid' => $orderId,
            ':type' => $type,
            ':fname' => $firstName,
            ':lname' => $lastName,
            ':email' => $data['email'] ?? '',
            ':phone' => $data['phone'] ?? '',
            ':addr' => $data['address'] ?? '',
            ':city' => $data['city'] ?? '',
            ':state' => '',
            ':zip' => $data['zip'] ?? '',
            ':country' => 'India'
        ]);
    }

    /**
     * Add payment info
     */
    public function addPaymentInfo(int $orderId, string $shippingMethod, string $paymentMethod): bool
    {
        $pdo = Database::getInstance()->getConnection();

        $stmt = $pdo->prepare("
            INSERT INTO sales_order_payment (order_id, shipping_method, payment_method)
            VALUES (:oid, :ship, :pay)
            ON CONFLICT (order_id) DO UPDATE SET
                shipping_method = EXCLUDED.shipping_method,
                payment_method = EXCLUDED.payment_method
        ");

        return $stmt->execute([
            ':oid' => $orderId,
            ':ship' => $shippingMethod,
            ':pay' => $paymentMethod
        ]);
    }
}
