<?php

namespace EasyCart\Model;

/**
 * Model_Product
 * 
 * Product entity with business logic.
 */
class Model_Product extends Model_Abstract
{
    /**
     * Check if product is in stock
     * @return bool
     */
    public function isInStock(): bool
    {
        $stock = $this->getData('stock');
        return $stock !== null && (int) $stock > 0;
    }

    /**
     * Get available stock quantity
     * @return int
     */
    public function getStock(): int
    {
        return (int) ($this->getData('stock') ?? 0);
    }

    /**
     * Check if product is featured
     * @return bool
     */
    public function isFeatured(): bool
    {
        return (bool) $this->getData('is_featured');
    }

    /**
     * Check if product is new
     * @return bool
     */
    public function isNew(): bool
    {
        return (bool) $this->getData('is_new');
    }

    /**
     * Get discount percentage
     * @return int
     */
    public function getDiscountPercent(): int
    {
        $price = (float) $this->getData('price');
        $originalPrice = (float) $this->getData('original_price');

        if ($originalPrice > $price && $originalPrice > 0) {
            return (int) round((($originalPrice - $price) / $originalPrice) * 100);
        }

        return 0;
    }

    /**
     * Get formatted price
     * @return string
     */
    public function getFormattedPrice(): string
    {
        return '$' . number_format((float) $this->getData('price'), 2);
    }

    /**
     * Get product name
     * @return string
     */
    public function getName(): string
    {
        return $this->getData('name') ?? '';
    }

    /**
     * Get product SKU
     * @return string
     */
    public function getSku(): string
    {
        return $this->getData('sku') ?? '';
    }

    /**
     * Get product price
     * @return float
     */
    public function getPrice(): float
    {
        return (float) ($this->getData('price') ?? 0);
    }

    /**
     * Get product rating
     * @return float
     */
    public function getRating(): float
    {
        return (float) ($this->getData('rating') ?? 0);
    }

    /**
     * Get brand name (from attributes)
     * @return string|null
     */
    public function getBrandName(): ?string
    {
        return $this->getData('brand_name');
    }

    /**
     * Get category ID
     * @return int|null
     */
    public function getCategoryId(): ?int
    {
        $catId = $this->getData('category_id');
        return $catId !== null ? (int) $catId : null;
    }
}
