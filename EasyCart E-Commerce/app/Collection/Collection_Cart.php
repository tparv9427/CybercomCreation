<?php

namespace EasyCart\Collection;

use EasyCart\Database\QueryBuilder;
use EasyCart\Core\Database;
use PDO;

/**
 * Collection_Cart — Cart Complex Queries
 * 
 * Handles cart items with product joins, saved-for-later items,
 * and cart summary calculations.
 */
class Collection_Cart extends Collection_Abstract
{
    protected $table = 'sales_cart';
    protected $primaryKey = 'cart_id';

    /**
     * Get cart items with product details
     */
    public function getCartItems(int $cartId): array
    {
        $sql = "SELECT ci.*, p.name, p.price, p.original_price, p.discount_percent, p.stock, p.icon, p.image, p.is_new,
                       (SELECT attribute_value FROM catalog_product_attribute WHERE product_entity_id = p.entity_id AND attribute_code = 'brand' LIMIT 1) as brand_name,
                       (SELECT image_path FROM catalog_product_image WHERE product_entity_id = p.entity_id AND is_primary = true LIMIT 1) as product_image
                FROM sales_cart_item ci
                JOIN catalog_product_entity p ON ci.product_id = p.entity_id
                WHERE ci.cart_id = :cart_id
                ORDER BY ci.created_at DESC";

        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':cart_id' => $cartId]);
        return $stmt->fetchAll();
    }

    /**
     * Get saved-for-later items
     */
    public function getSavedItems(int $cartId): array
    {
        $sql = "SELECT sfl.*, p.name, p.price, p.original_price, p.stock, p.icon, p.image
                FROM sales_cart_save_for_later sfl
                JOIN catalog_product_entity p ON sfl.product_id = p.entity_id
                WHERE sfl.cart_id = :cart_id
                ORDER BY sfl.created_at DESC";

        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':cart_id' => $cartId]);
        return $stmt->fetchAll();
    }

    /**
     * Find active cart by customer ID
     */
    public function findActiveByCustomer(int $customerId): ?array
    {
        return QueryBuilder::select($this->table, ['*'])
            ->where('customer_id', '=', $customerId)
            ->where('is_active', '=', true)
            ->orderBy('created_at', 'DESC')
            ->fetchOne();
    }

    /**
     * Find active cart by session ID
     */
    public function findActiveBySession(string $sessionId): ?array
    {
        return QueryBuilder::select($this->table, ['*'])
            ->where('session_id', '=', $sessionId)
            ->where('is_active', '=', true)
            ->where('customer_id', 'IS NULL')
            ->orderBy('created_at', 'DESC')
            ->fetchOne();
    }
}
