<?php

namespace EasyCart\Repositories;

use EasyCart\Core\Database;
use PDO;

/**
 * ProductRepository
 * 
 * Migrated to PostgreSQL
 */
class ProductRepository
{
    private $pdo;

    public function __construct()
    {
        $this->pdo = Database::getInstance()->getConnection();
    }

    public function getAll()
    {
        $stmt = $this->pdo->query("SELECT * FROM products ORDER BY id");
        $products = $stmt->fetchAll();
        return $this->processProducts($products);
    }

    public function find($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $product = $stmt->fetch();
        return $product ? $this->processProduct($product) : null;
    }

    public function getFeatured($limit = 20)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE is_featured = true LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $this->processProducts($stmt->fetchAll());
    }

    public function getNew($limit = 6)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE is_new = true LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $this->processProducts($stmt->fetchAll());
    }

    public function findByCategory($categoryId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE category_id = :id");
        $stmt->execute([':id' => $categoryId]);
        return $this->processProducts($stmt->fetchAll());
    }

    public function findByBrand($brandId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE brand_id = :id");
        $stmt->execute([':id' => $brandId]);
        return $this->processProducts($stmt->fetchAll());
    }

    // Helper to ensure data types match expected JSON format if needed
    private function processProduct($product)
    {
        if (!$product)
            return null;

        // Cast types
        $product['id'] = (int) $product['id'];
        $product['category_id'] = (int) $product['category_id'];
        $product['brand_id'] = (int) $product['brand_id'];
        $product['price'] = (float) $product['price'];
        if (isset($product['original_price']))
            $product['original_price'] = (float) $product['original_price'];
        if (isset($product['rating']))
            $product['rating'] = (float) $product['rating'];

        // Map 'is_new' from DB to 'new' expected by view
        $product['new'] = isset($product['is_new']) ? (bool) $product['is_new'] : false;

        // Mock 'discount_percent'
        if (!isset($product['discount_percent'])) {
            if (isset($product['original_price']) && $product['original_price'] > $product['price']) {
                $product['discount_percent'] = round((($product['original_price'] - $product['price']) / $product['original_price']) * 100);
            } else {
                $product['discount_percent'] = 0;
            }
        }

        // Mock 'trust_badges'
        if (!isset($product['trust_badges'])) {
            $product['trust_badges'] = ['secure', 'warranty', 'fast_delivery'];
        }

        // Mock 'icon' from 'image'
        if (!isset($product['icon']) && isset($product['image'])) {
            $imagePath = $product['image'];
            // If path doesn't start with assets, prepend the directory
            if (strpos($imagePath, 'assets/') !== 0) {
                $imagePath = 'assets/images/products/' . $imagePath;
            }
            $product['icon'] = '<img src="' . htmlspecialchars($imagePath) . '" alt="' . htmlspecialchars($product['name']) . '">';
        }

        // Mock 'long_description'
        if (!isset($product['long_description'])) {
            $product['long_description'] = $product['description'] ?? '';
        }

        // Mock 'features'
        if (!isset($product['features'])) {
            $product['features'] = [
                'High quality materials',
                'Durable and long-lasting',
                'Premium finish',
                'Best in class performance'
            ];
        }

        // Mock 'specifications'
        if (!isset($product['specifications'])) {
            $product['specifications'] = [
                'Material' => 'Premium Composite',
                'Warranty' => '1 Year',
                'Origin' => 'Imported',
                'Weight' => 'Lightweight'
            ];
        }

        // Mock 'bought_past_month'
        if (!isset($product['bought_past_month'])) {
            $product['bought_past_month'] = '50+';
        }

        return $product;
    }

    private function processProducts($products)
    {
        return array_map([$this, 'processProduct'], $products);
    }

    // --- In-Memory Filters (Kept for compatibility with Controllers) ---

    public function filterByPrice($products, $priceRange)
    {
        return array_filter($products, function ($product) use ($priceRange) {
            switch ($priceRange) {
                case 'under50':
                    return $product['price'] < 50;
                case '50-100':
                    return $product['price'] >= 50 && $product['price'] <= 100;
                case '100-200':
                    return $product['price'] > 100 && $product['price'] <= 200;
                case '200plus':
                    return $product['price'] > 200;
                default:
                    return true;
            }
        });
    }

    public function filterByRating($products, $rating)
    {
        return array_filter($products, function ($product) use ($rating) {
            return $product['rating'] >= $rating;
        });
    }

    public function getSimilarByBrand($currentProduct, $limit = 4)
    {
        // Optimized: fetching from DB directly
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE brand_id = :bid AND id != :pid LIMIT :limit");
        $stmt->bindValue(':bid', $currentProduct['brand_id'], PDO::PARAM_INT);
        $stmt->bindValue(':pid', $currentProduct['id'], PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $this->processProducts($stmt->fetchAll());
    }

    public function getSimilarByCategory($currentProduct, $limit = 4)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE category_id = :cid AND id != :pid LIMIT :limit");
        $stmt->bindValue(':cid', $currentProduct['category_id'], PDO::PARAM_INT);
        $stmt->bindValue(':pid', $currentProduct['id'], PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $this->processProducts($stmt->fetchAll());
    }

    public function getFromOtherCategories($currentProduct, $limit = 4)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE category_id != :cid AND id != :pid LIMIT :limit");
        $stmt->bindValue(':cid', $currentProduct['category_id'], PDO::PARAM_INT);
        $stmt->bindValue(':pid', $currentProduct['id'], PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $this->processProducts($stmt->fetchAll());
    }

    public function search($query)
    {
        if (empty($query))
            return [];
        $stmt = $this->pdo->prepare("SELECT * FROM products WHERE name ILIKE :q OR description ILIKE :q");
        $stmt->execute([':q' => "%$query%"]);
        return $this->processProducts($stmt->fetchAll());
    }
}
