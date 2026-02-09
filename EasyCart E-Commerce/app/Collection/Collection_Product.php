<?php

namespace EasyCart\Collection;

use EasyCart\Database\QueryBuilder;

/**
 * Collection_Product
 * 
 * Handles complex product queries with joins and filters.
 */
class Collection_Product extends Collection_Abstract
{
    /**
     * @var string
     */
    protected $resourceClass = \EasyCart\Resource\Resource_Product::class;

    /**
     * @var string
     */
    protected $modelClass = \EasyCart\Model\Model_Product::class;

    /**
     * Initialize SELECT with brand subquery
     */
    protected function initSelect(): void
    {
        $this->queryBuilder
            ->from('catalog_product_entity', 'p')
            ->select([
                'p.*',
                "(SELECT attribute_value FROM catalog_product_attribute WHERE product_entity_id = p.entity_id AND attribute_code = 'brand' LIMIT 1) as brand_name"
            ]);
    }

    /**
     * Filter by featured products
     * @return $this
     */
    public function addFeaturedFilter(): self
    {
        $this->queryBuilder->where('p.is_featured', true);
        return $this;
    }

    /**
     * Filter by new products
     * @return $this
     */
    public function addNewFilter(): self
    {
        $this->queryBuilder->where('p.is_new', true);
        return $this;
    }

    /**
     * Filter by category ID
     * @param int $categoryId
     * @return $this
     */
    public function addCategoryFilter(int $categoryId): self
    {
        $this->queryBuilder
            ->join('catalog_category_product cp', 'p.entity_id = cp.product_entity_id')
            ->where('cp.category_entity_id', $categoryId);
        return $this;
    }

    /**
     * Filter by brand name
     * @param string $brandName
     * @return $this
     */
    public function addBrandFilter(string $brandName): self
    {
        $this->queryBuilder
            ->join('catalog_product_attribute pa', 'p.entity_id = pa.product_entity_id')
            ->whereRaw("pa.attribute_code = 'brand'")
            ->where('pa.attribute_value', $brandName);
        return $this;
    }

    /**
     * Filter by price range
     * @param float $min
     * @param float $max
     * @return $this
     */
    public function addPriceFilter(float $min, float $max): self
    {
        $this->queryBuilder
            ->where('p.price', $min, '>=')
            ->where('p.price', $max, '<=');
        return $this;
    }

    /**
     * Filter by minimum rating
     * @param float $minRating
     * @return $this
     */
    public function addRatingFilter(float $minRating): self
    {
        $this->queryBuilder->where('p.rating', $minRating, '>=');
        return $this;
    }

    /**
     * Filter by search query (name or description)
     * @param string $query
     * @return $this
     */
    public function addSearchFilter(string $query): self
    {
        $pattern = "%{$query}%";
        $this->queryBuilder->whereRaw("(p.name ILIKE '{$pattern}' OR p.description ILIKE '{$pattern}')");
        return $this;
    }

    /**
     * Order by newest first
     * @return $this
     */
    public function setNewestFirst(): self
    {
        $this->queryBuilder->orderBy('p.entity_id', 'DESC');
        return $this;
    }

    /**
     * Order by price low to high
     * @return $this
     */
    public function setPriceLowToHigh(): self
    {
        $this->queryBuilder->orderBy('p.price', 'ASC');
        return $this;
    }

    /**
     * Order by price high to low
     * @return $this
     */
    public function setPriceHighToLow(): self
    {
        $this->queryBuilder->orderBy('p.price', 'DESC');
        return $this;
    }

    /**
     * Order by rating
     * @return $this
     */
    public function setRatingOrder(): self
    {
        $this->queryBuilder->orderBy('p.rating', 'DESC');
        return $this;
    }
}
