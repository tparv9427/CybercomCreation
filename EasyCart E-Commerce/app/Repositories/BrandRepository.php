<?php

namespace EasyCart\Repositories;

use EasyCart\Core\Database;
use EasyCart\Database\Queries;
use PDO;

/**
 * BrandRepository
 * 
 * Updated to use new schema: Brands are now stored as attributes
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
        $stmt = $this->pdo->query(Queries::BRAND_GET_ALL);
        $results = $stmt->fetchAll();

        // Re-key by name for compatibility (brands no longer have numeric IDs)
        $brands = [];
        $id = 1; // Generate sequential IDs for backward compatibility
        foreach ($results as $row) {
            $brands[$id] = [
                'id' => $id,
                'name' => $row['name']
            ];
            $id++;
        }
        return $brands;
    }

    public function find($id)
    {
        // Since brands are now attributes without numeric IDs,
        // we need to get all and find by index
        $brands = $this->getAll();
        return $brands[$id] ?? null;
    }

    public function findByName($name)
    {
        $stmt = $this->pdo->prepare(Queries::BRAND_FIND_BY_NAME);
        $stmt->execute([':name' => $name]);
        $result = $stmt->fetch();

        if ($result) {
            return [
                'id' => 0, // No numeric ID
                'name' => $result['name']
            ];
        }

        return null;
    }
}
