<?php

namespace EasyCart\Repositories;

use EasyCart\Core\Database;
use PDO;

/**
 * BrandRepository
 * 
 * Migrated to PostgreSQL
 */
class BrandRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function getAll()
    {
        $stmt = $this->pdo->query("SELECT * FROM brands ORDER BY id");
        $results = $stmt->fetchAll();

        // Re-key by ID for compatibility
        $brands = [];
        foreach ($results as $row) {
            $brands[$row['id']] = $row;
        }
        return $brands;
    }

    public function find($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM brands WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }
}
