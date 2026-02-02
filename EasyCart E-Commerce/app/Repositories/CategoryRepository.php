<?php

namespace EasyCart\Repositories;

use EasyCart\Core\Database;
use PDO;

/**
 * CategoryRepository
 * 
 * Migrated to PostgreSQL
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
        $stmt = $this->pdo->query("SELECT * FROM categories ORDER BY id");
        $results = $stmt->fetchAll();

        // Map to ID-keyed array to maintain compatibility with existing views if necessary
        // The existing views might iterate or look up by ID.
        // Array_column can re-index.
        $categories = [];
        foreach ($results as $row) {
            $categories[$row['id']] = $row;
        }
        return $categories;
    }

    public function find($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM categories WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }
}
