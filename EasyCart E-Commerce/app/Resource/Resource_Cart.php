<?php

namespace EasyCart\Resource;

use EasyCart\Database\QueryBuilder;

/**
 * Resource_Cart — Cart DB Configuration
 * 
 * Table: sales_cart
 * Primary Key: cart_id
 */
class Resource_Cart extends Resource_Abstract
{
    protected $table = 'sales_cart';
    protected $primaryKey = 'cart_id';
    protected $columns = [
        'cart_id',
        'customer_id',
        'session_id',
        'is_active',
        'items_count',
        'grand_total',
        'created_at',
        'updated_at'
    ];

    /**
     * Find active cart by user or session
     */
    public function findActive(int $userId = null, string $sessionId = null): ?array
    {
        $qb = QueryBuilder::select($this->table, ['*'])
            ->where('is_active', '=', true);

        if ($userId) {
            $qb->where('customer_id', '=', $userId); // Note: field is customer_id in schema
        } elseif ($sessionId) {
            $qb->where('session_id', '=', $sessionId);
        } else {
            return null;
        }

        return $qb->fetchOne();
    }

    /**
     * Get cart items [product_entity_id => quantity]
     */
    public function getItems(int $cartId): array
    {
        $rows = QueryBuilder::select('sales_cart_product', ['product_entity_id', 'quantity'])
            ->where('cart_id', '=', $cartId)
            ->fetchAll();

        $items = [];
        foreach ($rows as $row) {
            $items[$row['product_entity_id']] = $row['quantity'];
        }
        return $items;
    }

    /**
     * Add or update item in cart
     */
    public function saveItem(int $cartId, int $productId, int $quantity): void
    {
        // Check uniqueness via select first since we don't have UPSERT/ON CONFLICT in QB yet
        // OR use raw query if QB supports raw?
        // Let's implement simple check-update/insert logic

        $exists = QueryBuilder::select('sales_cart_product', ['quantity'])
            ->where('cart_id', '=', $cartId)
            ->where('product_entity_id', '=', $productId)
            ->fetchOne();

        if ($exists) {
            QueryBuilder::update('sales_cart_product', ['quantity' => $quantity])
                ->where('cart_id', '=', $cartId)
                ->where('product_entity_id', '=', $productId)
                ->execute();
        } else {
            QueryBuilder::insert('sales_cart_product', [
                'cart_id' => $cartId,
                'product_entity_id' => $productId,
                'quantity' => $quantity
            ])->execute();
        }
    }

    /**
     * Remove item from cart
     */
    public function removeItem(int $cartId, int $productId): void
    {
        QueryBuilder::delete('sales_cart_product')
            ->where('cart_id', '=', $cartId)
            ->where('product_entity_id', '=', $productId)
            ->execute();
    }

    /**
     * Clear all items from cart
     */
    public function clearItems(int $cartId): void
    {
        QueryBuilder::delete('sales_cart_product')
            ->where('cart_id', '=', $cartId)
            ->execute();
    }
}
