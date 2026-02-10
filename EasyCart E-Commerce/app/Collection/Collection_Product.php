<?php

namespace EasyCart\Collection;

use EasyCart\Database\QueryBuilder;
use EasyCart\Core\Database;
use PDO;

/**
 * Collection_Product — Product Complex Queries
 * 
 * Handles joins, filters, search, pagination for products.
 * Replaces ProductRepository query logic.
 */
class Collection_Product extends Collection_Abstract
{
    protected $table = 'catalog_product_entity';
    protected $alias = 'p';
    protected $primaryKey = 'entity_id';

    /**
     * Brand subquery used across various queries
     */
    private function brandSubquery(): string
    {
        return "(SELECT attribute_value FROM catalog_product_attribute WHERE product_entity_id = p.entity_id AND attribute_code = 'brand' LIMIT 1) as brand_name";
    }

    /**
     * Image subquery
     */
    private function imageSubquery(): string
    {
        return "(SELECT image_path FROM catalog_product_image WHERE product_entity_id = p.entity_id AND is_primary = true LIMIT 1) as image";
    }

    /**
     * Category ID subquery
     */
    private function categoryIdSubquery(): string
    {
        return "(SELECT category_entity_id FROM catalog_category_product WHERE product_entity_id = p.entity_id LIMIT 1) as category_id";
    }

    /**
     * Base product columns for listing
     * @return array
     */
    private function baseColumns(): array
    {
        return [
            'p.*',
            $this->brandSubquery(),
            $this->imageSubquery(),
            $this->categoryIdSubquery()
        ];
    }

    /**
     * Get all products with optional pagination
     */
    public function getAll(?int $limit = null, int $offset = 0): array
    {
        $qb = QueryBuilder::select($this->table, $this->baseColumns())
            ->alias('p')
            ->orderBy('p.entity_id', 'ASC');

        if ($limit !== null) {
            $qb->limit($limit)->offset($offset);
        }

        return $this->processRows($qb->fetchAll());
    }

    /**
     * Count all products
     */
    public function countAll(): int
    {
        return (int) QueryBuilder::select($this->table, ['COUNT(*) as total'])
            ->fetchColumn();
    }

    /**
     * Find single product by ID
     */
    public function findById(int $id): ?array
    {
        $row = QueryBuilder::select($this->table, $this->baseColumns())
            ->alias('p')
            ->where('p.entity_id', '=', $id)
            ->fetchOne();

        return $row ? $this->processRow($row) : null;
    }

    /**
     * Find product by URL key
     */
    public function findByUrlKey(string $urlKey): ?array
    {
        $row = QueryBuilder::select($this->table, $this->baseColumns())
            ->alias('p')
            ->where('p.url_key', '=', $urlKey)
            ->fetchOne();

        return $row ? $this->processRow($row) : null;
    }

    /**
     * Get featured products
     */
    public function getFeatured(int $limit = 20): array
    {
        $rows = QueryBuilder::select($this->table, $this->baseColumns())
            ->alias('p')
            ->where('p.is_featured', '=', true)
            ->limit($limit)
            ->fetchAll();

        return $this->processRows($rows);
    }

    /**
     * Get new products
     */
    public function getNew(int $limit = 6): array
    {
        $rows = QueryBuilder::select($this->table, $this->baseColumns())
            ->alias('p')
            ->where('p.is_new', '=', true)
            ->limit($limit)
            ->fetchAll();

        return $this->processRows($rows);
    }

    /**
     * Get products by category
     */
    public function findByCategory(int $categoryId, ?int $limit = null, int $offset = 0): array
    {
        $qb = QueryBuilder::select($this->table, array_merge($this->baseColumns(), ['cp.position as category_position']))
            ->alias('p')
            ->distinct()
            ->join('catalog_category_product cp', 'p.entity_id = cp.product_entity_id')
            ->where('cp.category_entity_id', '=', $categoryId)
            ->orderBy('cp.position', 'ASC')
            ->orderBy('p.entity_id', 'ASC');

        if ($limit !== null) {
            $qb->limit($limit)->offset($offset);
        }

        return $this->processRows($qb->fetchAll());
    }

    /**
     * Count products in a category
     */
    public function countByCategory(int $categoryId): int
    {
        return (int) QueryBuilder::select($this->table, ['COUNT(DISTINCT p.entity_id) as total'])
            ->alias('p')
            ->join('catalog_category_product cp', 'p.entity_id = cp.product_entity_id')
            ->where('cp.category_entity_id', '=', $categoryId)
            ->fetchColumn();
    }

    /**
     * Get products by brand name
     */
    public function findByBrand(string $brandName, ?int $limit = null, int $offset = 0): array
    {
        $qb = QueryBuilder::select($this->table, $this->baseColumns())
            ->alias('p')
            ->join('catalog_product_attribute pa', "p.entity_id = pa.product_entity_id AND pa.attribute_code = 'brand'")
            ->where('pa.attribute_value', '=', $brandName)
            ->orderBy('p.entity_id', 'ASC');

        if ($limit !== null) {
            $qb->limit($limit)->offset($offset);
        }

        return $this->processRows($qb->fetchAll());
    }

    /**
     * Count products by brand
     */
    public function countByBrand(string $brandName): int
    {
        return (int) QueryBuilder::select($this->table, ['COUNT(*) as total'])
            ->alias('p')
            ->join('catalog_product_attribute pa', "p.entity_id = pa.product_entity_id AND pa.attribute_code = 'brand'")
            ->where('pa.attribute_value', '=', $brandName)
            ->fetchColumn();
    }

    /**
     * Get similar products by brand
     */
    public function getSimilarByBrand(array $currentProduct, int $limit = 4): array
    {
        if (empty($currentProduct['brand_name'])) {
            return [];
        }

        $rows = QueryBuilder::select($this->table, $this->baseColumns())
            ->alias('p')
            ->join('catalog_product_attribute pa', "p.entity_id = pa.product_entity_id AND pa.attribute_code = 'brand'")
            ->where('pa.attribute_value', '=', $currentProduct['brand_name'])
            ->where('p.entity_id', '!=', $currentProduct['entity_id'] ?? $currentProduct['id'])
            ->limit($limit)
            ->fetchAll();

        return $this->processRows($rows);
    }

    /**
     * Get similar products by category
     */
    public function getSimilarByCategory(array $currentProduct, int $limit = 4): array
    {
        $catId = $currentProduct['category_id'] ?? null;
        if (!$catId) {
            return [];
        }

        $rows = QueryBuilder::select($this->table, $this->baseColumns())
            ->alias('p')
            ->join('catalog_category_product cp', 'p.entity_id = cp.product_entity_id')
            ->where('cp.category_entity_id', '=', $catId)
            ->where('p.entity_id', '!=', $currentProduct['entity_id'] ?? $currentProduct['id'])
            ->limit($limit)
            ->fetchAll();

        return $this->processRows($rows);
    }

    /**
     * Get products from other categories
     */
    public function getFromOtherCategories(array $currentProduct, int $limit = 4): array
    {
        $catId = $currentProduct['category_id'] ?? null;
        $prodId = $currentProduct['entity_id'] ?? $currentProduct['id'];

        $sql = "SELECT p.*, "
            . $this->brandSubquery() . ", "
            . $this->imageSubquery()
            . " FROM {$this->table} p"
            . " WHERE p.entity_id != :prod_id"
            . ($catId ? " AND p.entity_id NOT IN (SELECT product_entity_id FROM catalog_category_product WHERE category_entity_id = :cat_id)" : "")
            . " LIMIT :lim";

        $bindings = [':prod_id' => $prodId, ':lim' => $limit];
        if ($catId) {
            $bindings[':cat_id'] = $catId;
        }

        $pdo = Database::getInstance()->getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute($bindings);
        return $this->processRows($stmt->fetchAll());
    }

    /**
     * Search products by query
     */
    public function search(string $query, ?int $limit = null, int $offset = 0): array
    {
        $term = '%' . $query . '%';

        $qb = QueryBuilder::select($this->table, $this->baseColumns())
            ->alias('p')
            ->where('p.name', 'LIKE', $term)
            ->orderBy('p.entity_id', 'ASC');

        if ($limit !== null) {
            $qb->limit($limit)->offset($offset);
        }

        return $this->processRows($qb->fetchAll());
    }

    /**
     * Count search results
     */
    public function countSearch(string $query): int
    {
        $term = '%' . $query . '%';

        return (int) QueryBuilder::select($this->table, ['COUNT(*) as total'])
            ->alias('p')
            ->where('p.name', 'LIKE', $term)
            ->fetchColumn();
    }

    /**
     * Filter products by price range
     */
    public function filterByPrice(array $products, ?string $priceRange): array
    {
        if (!$priceRange) {
            return $products;
        }
        return array_filter($products, function ($p) use ($priceRange) {
            $price = (float) ($p['price'] ?? 0);
            switch ($priceRange) {
                case 'under25':
                    return $price < 25;
                case '25to50':
                    return $price >= 25 && $price <= 50;
                case '50to100':
                    return $price >= 50 && $price <= 100;
                case 'over100':
                    return $price > 100;
                default:
                    return true;
            }
        });
    }

    /**
     * Filter products by minimum rating
     */
    public function filterByRating(array $products, ?float $rating): array
    {
        if (!$rating) {
            return $products;
        }
        return array_filter($products, fn($p) => ($p['rating'] ?? 0) >= $rating);
    }

    /**
     * Process a product row — adds backward-compatible fields
     */
    protected function processRow(array $row): array
    {
        // Backward compatibility: entity_id → id
        $row['id'] = (int) ($row['entity_id'] ?? $row['id'] ?? 0);
        $row['price'] = (float) ($row['price'] ?? 0);

        if (isset($row['original_price'])) {
            $row['original_price'] = (float) $row['original_price'];
        }
        if (isset($row['rating'])) {
            $row['rating'] = (float) $row['rating'];
        }

        // Map is_new → new for views
        $row['new'] = isset($row['is_new']) ? (bool) $row['is_new'] : false;

        // Calculate discount_percent if missing
        if (!isset($row['discount_percent'])) {
            if (isset($row['original_price']) && $row['original_price'] > $row['price']) {
                $row['discount_percent'] = round((($row['original_price'] - $row['price']) / $row['original_price']) * 100);
            } else {
                $row['discount_percent'] = 0;
            }
        }

        // Defaults
        if (!isset($row['trust_badges'])) {
            $row['trust_badges'] = ['secure', 'warranty', 'fast_delivery'];
        }

        // Process image into icon HTML
        if (!isset($row['icon']) && isset($row['image'])) {
            $imagePath = $row['image'];
            if (strpos($imagePath, '/assets/') !== 0 && strpos($imagePath, 'assets/') !== 0) {
                $imagePath = '/assets/images/products/' . $imagePath;
            } elseif (strpos($imagePath, 'assets/') === 0) {
                $imagePath = '/' . $imagePath;
            }
            $row['icon'] = '<img src="' . htmlspecialchars($imagePath) . '" alt="' . htmlspecialchars($row['name'] ?? '') . '">';
        }

        // Defaults for missing fields
        if (!isset($row['long_description'])) {
            $row['long_description'] = $row['description'] ?? '';
        }
        if (!isset($row['features'])) {
            $row['features'] = ['High quality materials', 'Durable and long-lasting', 'Premium finish', 'Best in class performance'];
        }
        if (!isset($row['specifications'])) {
            $row['specifications'] = ['Material' => 'Premium Composite', 'Warranty' => '1 Year', 'Origin' => 'Imported', 'Weight' => 'Lightweight'];
        }
        if (!isset($row['slug']) || empty($row['slug'])) {
            $name = $row['name'] ?? 'product-' . $row['id'];
            $row['slug'] = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
        }
        if (!isset($row['reviews_count'])) {
            $row['reviews_count'] = 125;
        }
        if (!isset($row['stock'])) {
            $row['stock'] = 10;
        }

        return $row;
    }
}
