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
        return $this->resource->findByRequestPath($path);
    }

    /**
     * Create or update a product slug
     */
    public function saveProductRewrite(int $productId, string $slug, bool $isCanonical = true): void
    {
        $slug = $this->normalizeSlug($slug);
        $requestPath = 'p/' . $slug;

        // Check if this path is already taken by ANOTHER entity
        $existing = $this->resource->findByRequestPath($requestPath);
        if ($existing && ($existing['entity_type'] !== 'product' || $existing['entity_id'] != $productId)) {
            // Path collision - append ID or index
            $requestPath .= '-' . $productId;
        }

        // Check if we already have a canonical rewrite for this product
        $canonical = $this->resource->findByEntity('product', $productId);

        if ($canonical) {
            if ($canonical['request_path'] === $requestPath) {
                return; // Nothing to change
            }

            // If we are changing canonical slug, the old one should become a 301 redirect
            // Requirement 5: Slugs must NOT change automatically when names are edited.
            // So we only call this when explicitly asked to update URLs.
            $this->resource->update($canonical['url_rewrite_id'], [
                'redirect_type' => 301,
                'target_path' => $requestPath
            ]);
        }

        // Save new rewrite
        $this->resource->save([
            'request_path' => $requestPath,
            'target_path' => 'product/view/' . $productId,
            'entity_id' => $productId,
            'entity_type' => 'product',
            'redirect_type' => 0
        ]);

        // Constraint: user requested to update the column in the product table as well
        if ($isCanonical) {
            $db = \EasyCart\Core\Database::getInstance()->getConnection();

            // Extract slug (remove 'p/' prefix)
            $urlKey = substr($requestPath, 2);

            $stmt = $db->prepare("UPDATE catalog_product_entity SET url_key = :url_key WHERE entity_id = :id");
            $stmt->execute([
                ':url_key' => $urlKey,
                ':id' => $productId
            ]);
        }
    }

    /**
     * Get canonical URL for a product
     */
    public function getProductUrl(array $product): string
    {
        $productId = $product['id'] ?? $product['entity_id'] ?? 0;
        $rewrite = $this->resource->findByEntity('product', $productId);

        if ($rewrite) {
            return '/' . $rewrite['request_path'];
        }

        // Fallback to ID-based if no rewrite exists (should not happen after migration)
        return '/product/' . $productId;
    }

    /**
     * Normalize slug: lowercase, hyphenated, SEO-safe
     */
    public function normalizeSlug(string $text): string
    {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        $text = strtolower($text);

        return empty($text) ? 'n-a' : $text;
    }
}
