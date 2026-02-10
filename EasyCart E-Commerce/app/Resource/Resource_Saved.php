<?php

namespace EasyCart\Resource;

use EasyCart\Database\QueryBuilder;

/**
 * Resource_Saved — Saved for Later DB Configuration
 * 
 * Table: saved_items
 * Composite Key: user_id, product_id
 */
class Resource_Saved extends Resource_Abstract
{
    protected $table = 'saved_items';
    protected $primaryKey = 'id'; // Placeholder
    protected $columns = [
        'user_id',
        'product_id',
        'created_at'
    ];

    /**
     * Get all product IDs in user's saved list
     * 
     * @param int $userId
     * @return array
     */
    public function getByUserId(int $userId): array
    {
        $rows = QueryBuilder::select($this->table, ['product_id'])
            ->where('user_id', '=', $userId)
            ->fetchAll();
        return array_column($rows, 'product_id');
    }

    /**
     * Add product to saved items
     * 
     * @param int $userId
     * @param int $productId
     * @return void
     */
    public function add(int $userId, int $productId): void
    {
        if (!$this->existsPair($userId, $productId)) {
            QueryBuilder::insert($this->table, [
                'user_id' => $userId,
                'product_id' => $productId
            ])->execute();
        }
    }

    /**
     * Remove product from saved items
     * 
     * @param int $userId
     * @param int $productId
     * @return void
     */
    public function remove(int $userId, int $productId): void
    {
        QueryBuilder::delete($this->table)
            ->where('user_id', '=', $userId)
            ->where('product_id', '=', $productId)
            ->execute();
    }

    /**
     * Check if pair exists
     */
    public function existsPair(int $userId, int $productId): bool
    {
        $res = QueryBuilder::select($this->table, ['COUNT(*) as cnt'])
            ->where('user_id', '=', $userId)
            ->where('product_id', '=', $productId)
            ->fetchOne();
        return ($res['cnt'] ?? 0) > 0;
    }
}
