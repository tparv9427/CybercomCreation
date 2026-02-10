<?php

namespace EasyCart\Collection;

use EasyCart\Database\QueryBuilder;

/**
 * Collection_Category — Category Complex Queries
 * 
 * Handles category retrieval, tree building, and product associations.
 */
class Collection_Category extends Collection_Abstract
{
    protected $table = 'catalog_category_entity';
    protected $primaryKey = 'entity_id';

    /**
     * Get all categories keyed by ID (backward-compatible format)
     */
    public function getAll(): array
    {
        $rows = QueryBuilder::select($this->table, ['*'])
            ->orderBy('position', 'ASC')
            ->orderBy('entity_id', 'ASC')
            ->fetchAll();

        $categories = [];
        foreach ($rows as $row) {
            $row['id'] = $row['entity_id'];
            $categories[$row['entity_id']] = $row;
        }
        return $categories;
    }

    /**
     * Find category by ID
     */
    public function findById(int $id): ?array
    {
        $row = QueryBuilder::select($this->table, ['*'])
            ->where('entity_id', '=', $id)
            ->fetchOne();

        if ($row) {
            $row['id'] = $row['entity_id'];
        }

        return $row;
    }

    /**
     * Find category by name
     */
    public function findByName(string $name): ?array
    {
        $row = QueryBuilder::select($this->table, ['*'])
            ->where('name', '=', $name)
            ->fetchOne();

        if ($row) {
            $row['id'] = $row['entity_id'];
        }

        return $row;
    }

    /**
     * Get products in a category
     */
    public function getCategoryProducts(int $categoryId): array
    {
        return QueryBuilder::select('catalog_category_product', ['*'])
            ->where('category_entity_id', '=', $categoryId)
            ->fetchAll();
    }
}
