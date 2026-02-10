<?php

namespace EasyCart\Resource;

use EasyCart\Database\QueryBuilder;

/**
 * Resource_Wishlist — Wishlist DB Configuration
 * 
 * Table: catalog_wishlist
 * Composite Key: user_id, product_entity_id
 */
class Resource_Wishlist extends Resource_Abstract
{
    protected $table = 'catalog_wishlist';
    protected $primaryKey = 'wishlist_id'; // Likely doesn't exist, but required by abstract
    protected $columns = [
        'user_id',
        'product_entity_id',
        'created_at'
    ];

    /**
     * Get all product IDs in user's wishlist
     * 
     * @param int $userId
     * @return array
     */
    public function getByUserId(int $userId): array
    {
        $rows = QueryBuilder::select($this->table, ['product_entity_id'])
            ->where('user_id', '=', $userId)
            ->fetchAll();
        return array_column($rows, 'product_entity_id');
    }

    /**
     * Add product to wishlist
     * 
     * @param int $userId
     * @param int $productId
     * @return void
     */
    public function add(int $userId, int $productId): void
    {
        // Use raw SQL for ON CONFLICT or check existence first (easier with QB usually)
        // Since we don't have ON CONFLICT support in QB yet, we check exist
        if (!$this->existsPair($userId, $productId)) {
            QueryBuilder::insert($this->table, [
                'user_id' => $userId,
                'product_entity_id' => $productId
            ])->execute();
        }
    }

    /**
     * Remove product from wishlist
     * 
     * @param int $userId
     * @param int $productId
     * @return void
     */
    public function remove(int $userId, int $productId): void
    {
        QueryBuilder::delete($this->table)
            ->where('user_id', '=', $userId)
            ->where('product_entity_id', '=', $productId)
            ->execute();
    }

    /**
     * Check if pair exists
     */
    public function existsPair(int $userId, int $productId): bool
    {
        $res = QueryBuilder::select($this->table, ['COUNT(*) as cnt'])
            ->where('user_id', '=', $userId)
            ->where('product_entity_id', '=', $productId)
            ->fetchOne();
        return ($res['cnt'] ?? 0) > 0;
    }
}
