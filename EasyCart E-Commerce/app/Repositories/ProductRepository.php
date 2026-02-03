<?php

namespace EasyCart\Repositories;

use EasyCart\Core\Database;
use EasyCart\Database\Queries;
use PDO;

/**
 * ProductRepository
 * 
 * Updated to use new schema with centralized queries
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
        $stmt = $this->pdo->query(Queries::PRODUCT_GET_ALL);
        $products = $stmt->fetchAll();
        return $this->processProducts($products);
    }

    public function find($id)
    {
        $stmt = $this->pdo->prepare(Queries::PRODUCT_FIND_BY_ID);
        $stmt->execute([':id' => $id]);
        $product = $stmt->fetch();
        return $product ? $this->processProduct($product) : null;
    }

    public function getFeatured($limit = 20)
    {
        $stmt = $this->pdo->prepare(Queries::PRODUCT_GET_FEATURED);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $this->processProducts($stmt->fetchAll());
    }

    public function getNew($limit = 6)
    {
        $stmt = $this->pdo->prepare(Queries::PRODUCT_GET_NEW);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $this->processProducts($stmt->fetchAll());
    }

    public function findByCategory($categoryId)
    {
        $stmt = $this->pdo->prepare(Queries::PRODUCT_FIND_BY_CATEGORY);
        $stmt->execute([':id' => $categoryId]);
        return $this->processProducts($stmt->fetchAll());
    }

    public function findByBrand($brandName)
    {
        $stmt = $this->pdo->prepare(Queries::PRODUCT_FIND_BY_BRAND);
        $stmt->execute([':brand_name' => $brandName]);
        return $this->processProducts($stmt->fetchAll());
    }

    // Helper to ensure data types match expected JSON format if needed
    private function processProduct($product)
    {
        if (!$product)
            return null;

        // Map new schema fields to old field names for compatibility
        $product['id'] = (int) $product['entity_id'];
        $product['brand_id'] = null; // No longer used, brand is in attributes
        $product['price'] = (float) $product['price'];

        // Get category_id from catalog_category_product table
        if (!isset($product['category_id']) || !$product['category_id']) {
            $catStmt = $this->pdo->prepare(Queries::PRODUCT_GET_CATEGORY_ID);
            $catStmt->execute([':pid' => $product['entity_id']]);
            $product['category_id'] = $catStmt->fetchColumn() ?: null;
        }

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

        // Get primary image from catalog_product_image table
        if (!isset($product['image'])) {
            $imageStmt = $this->pdo->prepare(Queries::PRODUCT_GET_PRIMARY_IMAGE);
            $imageStmt->execute([':product_id' => $product['entity_id']]);
            $imagePath = $imageStmt->fetchColumn();

            if ($imagePath) {
                $product['image'] = $imagePath;
            }
        }

        // Mock 'icon' from 'image'
        if (!isset($product['icon']) && isset($product['image'])) {
            $imagePath = $product['image'];
            // If path doesn't start with /assets, prepend the directory
            if (strpos($imagePath, '/assets/') !== 0 && strpos($imagePath, 'assets/') !== 0) {
                $imagePath = '/assets/images/products/' . $imagePath;
            } elseif (strpos($imagePath, 'assets/') === 0) {
                $imagePath = '/' . $imagePath;
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

        // Ensure 'slug' is set (used for SKU display in view)
        if (!isset($product['slug']) || empty($product['slug'])) {
            // Generate a slug from name if missing, or use ID
            $name = $product['name'] ?? 'product-' . $product['id'];
            $product['slug'] = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
            if (empty($product['slug'])) {
                $product['slug'] = 'sku-' . $product['id'];
            }
        }

        // Mock 'reviews_count' if missing
        if (!isset($product['reviews_count'])) {
            $product['reviews_count'] = 125;
        }

        // Ensure 'stock' is set
        if (!isset($product['stock'])) {
            $product['stock'] = 10; // Default to in-stock if unknown, or 0 safely
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
        // Get brand name from current product
        $brandName = $currentProduct['brand_name'] ?? null;

        if (!$brandName) {
            return [];
        }

        $stmt = $this->pdo->prepare(Queries::PRODUCT_SIMILAR_BY_BRAND);
        $stmt->bindValue(':brand_name', $brandName, PDO::PARAM_STR);
        $stmt->bindValue(':pid', $currentProduct['entity_id'], PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $this->processProducts($stmt->fetchAll());
    }

    public function getSimilarByCategory($currentProduct, $limit = 4)
    {
        // Get category from current product
        $categoryStmt = $this->pdo->prepare(Queries::PRODUCT_GET_CATEGORY_ID);
        $categoryStmt->execute([':pid' => $currentProduct['entity_id']]);
        $categoryId = $categoryStmt->fetchColumn();

        if (!$categoryId) {
            return [];
        }

        $stmt = $this->pdo->prepare(Queries::PRODUCT_SIMILAR_BY_CATEGORY);
        $stmt->bindValue(':cid', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':pid', $currentProduct['entity_id'], PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $this->processProducts($stmt->fetchAll());
    }

    public function getFromOtherCategories($currentProduct, $limit = 4)
    {
        // Get category from current product
        $categoryStmt = $this->pdo->prepare(Queries::PRODUCT_GET_CATEGORY_ID);
        $categoryStmt->execute([':pid' => $currentProduct['entity_id']]);
        $categoryId = $categoryStmt->fetchColumn();

        $stmt = $this->pdo->prepare(Queries::PRODUCT_FROM_OTHER_CATEGORIES);
        $stmt->bindValue(':cid', $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(':pid', $currentProduct['entity_id'], PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $this->processProducts($stmt->fetchAll());
    }

    public function search($query)
    {
        if (empty($query))
            return [];

        $stmt = $this->pdo->prepare(Queries::PRODUCT_SEARCH);
        $stmt->execute([':q' => "%$query%"]);
        return $this->processProducts($stmt->fetchAll());
    }
}
