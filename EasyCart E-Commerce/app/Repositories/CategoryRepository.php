<?php

namespace EasyCart\Repositories;

use EasyCart\Core\Database;
use EasyCart\Database\Queries;
use PDO;

/**
 * CategoryRepository
 * 
 * Updated to use new schema with centralized queries
 */
class CategoryRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function getAll()
    {
        $stmt = $this->pdo->query(Queries::CATEGORY_GET_ALL);
        $results = $stmt->fetchAll();

        // Map to ID-keyed array to maintain compatibility with existing views
        // Map entity_id to id for backward compatibility
        $categories = [];
        foreach ($results as $row) {
            $row['id'] = $row['entity_id']; // Backward compatibility
            $categories[$row['entity_id']] = $row;
        }
        return $categories;
    }

    public function find($id)
    {
        $stmt = $this->pdo->prepare(Queries::CATEGORY_FIND_BY_ID);
        $stmt->execute([':id' => $id]);
        $category = $stmt->fetch();

        if ($category) {
            $category['id'] = $category['entity_id']; // Backward compatibility
        }

        return $category ?: null;
    }

    /**
     * Get products in a category
     */
    public function getCategoryProducts($categoryId)
    {
        $stmt = $this->pdo->prepare(Queries::CATEGORY_GET_PRODUCTS);
        $stmt->execute([':category_id' => $categoryId]);
        return $stmt->fetchAll();
    }
}
