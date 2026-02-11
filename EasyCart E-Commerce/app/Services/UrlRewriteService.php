<?php

namespace EasyCart\Services;

use EasyCart\Resource\Resource_UrlRewrite;

/**
 * UrlRewriteService — Manages SEO-friendly URLs and redirects
 */
class UrlRewriteService
{
    private $resource;

    public function __construct()
    {
        $this->resource = new Resource_UrlRewrite();
    }

    /**
     * Resolve a URL path to its target
     */
    public function resolve(string $path): ?array
    {
        $path = trim($path, '/');
        if (empty($path))
            return null;
        return $this->resource->findByRequestPath($path);
    }

    /**
     * Create or update a product slug (ROOT-LEVEL)
     */
    public function saveProductRewrite(int $productId, string $slug, bool $isCanonical = true): void
    {
        $slug = $this->normalizeSlug($slug);
        $requestPath = $slug;

        // Check for collisions with static routes or other rewrites
        $requestPath = $this->resolveCollision($requestPath, 'product', $productId);

        // Check if we already have a canonical rewrite for this product
        $canonical = $this->resource->findByEntity('product', $productId);

        if ($canonical) {
            if ($canonical['request_path'] === $requestPath) {
                return; // Nothing to change
            }

            // Create 301 redirect from old path
            $this->resource->save([
                'request_path' => $canonical['request_path'],
                'target_path' => $requestPath,
                'entity_id' => $productId,
                'entity_type' => 'product',
                'redirect_type' => 301
            ]);

            // Update current canonical
            $this->resource->update($canonical['url_rewrite_id'], [
                'request_path' => $requestPath
            ]);
        } else {
            // Save new rewrite
            $this->resource->save([
                'request_path' => $requestPath,
                'target_path' => 'product/view/' . $productId,
                'entity_id' => $productId,
                'entity_type' => 'product',
                'redirect_type' => 0
            ]);
        }

        // Update url_key in main table
        if ($isCanonical) {
            $db = \EasyCart\Core\Database::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE catalog_product_entity SET url_key = :url_key WHERE entity_id = :id");
            $stmt->execute([':url_key' => $requestPath, ':id' => $productId]);
        }
    }

    /**
     * Create or update a category slug (ROOT-LEVEL)
     */
    public function saveCategoryRewrite(int $categoryId, string $slug): void
    {
        $slug = $this->normalizeSlug($slug);
        $requestPath = $this->resolveCollision($slug, 'category', $categoryId);

        $canonical = $this->resource->findByEntity('category', $categoryId);

        if ($canonical) {
            if ($canonical['request_path'] === $requestPath)
                return;
            $this->resource->update($canonical['url_rewrite_id'], ['request_path' => $requestPath]);
        } else {
            $this->resource->save([
                'request_path' => $requestPath,
                'target_path' => 'products?category=' . $categoryId,
                'entity_id' => $categoryId,
                'entity_type' => 'category',
                'redirect_type' => 0
            ]);
        }

        // Update url_key in category table
        $db = \EasyCart\Core\Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE catalog_category_entity SET url_key = :url_key WHERE entity_id = :id");
        $stmt->execute([':url_key' => $requestPath, ':id' => $categoryId]);
    }

    /**
     * Create or update a brand slug (ROOT-LEVEL)
     */
    public function saveBrandRewrite(int $brandId, string $brandName): void
    {
        $slug = $this->normalizeSlug($brandName);
        $requestPath = $this->resolveCollision($slug, 'brand', $brandId);

        $canonical = $this->resource->findByEntity('brand', $brandId);

        if ($canonical) {
            if ($canonical['request_path'] === $requestPath)
                return;
            $this->resource->update($canonical['url_rewrite_id'], ['request_path' => $requestPath]);
        } else {
            $this->resource->save([
                'request_path' => $requestPath,
                'target_path' => 'brand/' . $brandId,
                'entity_id' => $brandId,
                'entity_type' => 'brand',
                'redirect_type' => 0
            ]);
        }
    }

    /**
     * Resolve path collision by appending ID if necessary
     */
    private function resolveCollision(string $path, string $type, int $id): string
    {
        $existing = $this->resource->findByRequestPath($path);
        if ($existing && ($existing['entity_type'] !== $type || $existing['entity_id'] != $id)) {
            return $path . '-' . $id;
        }
        return $path;
    }

    /**
     * Get canonical URL for a product
     */
    public function getProductUrl(array $product): string
    {
        $productId = $product['id'] ?? $product['entity_id'] ?? 0;
        $rewrite = $this->resource->findByEntity('product', $productId);
        return $rewrite ? '/' . $rewrite['request_path'] : '/product/' . $productId;
    }

    /**
     * Get canonical URL for a category
     */
    public function getCategoryUrl(array $category): string
    {
        $categoryId = $category['id'] ?? $category['entity_id'] ?? 0;
        $rewrite = $this->resource->findByEntity('category', $categoryId);
        return $rewrite ? '/' . $rewrite['request_path'] : '/products?category=' . $categoryId;
    }

    /**
     * Get canonical URL for a brand
     */
    public function getBrandUrl(array $brand): string
    {
        $brandId = $brand['id'] ?? 0;
        $rewrite = $this->resource->findByEntity('brand', $brandId);
        return $rewrite ? '/' . $rewrite['request_path'] : '/brand/' . $brandId;
    }

    /**
     * Normalize slug: lowercase, hyphenated, SEO-safe
     */
    public function normalizeSlug(string $text): string
    {
        $text = strtolower(trim($text));
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        if (function_exists('iconv')) {
            $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        }
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);

        return empty($text) ? 'n-a' : $text;
    }
}
