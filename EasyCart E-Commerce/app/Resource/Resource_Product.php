<?php

namespace EasyCart\Resource;

/**
 * Resource_Product
 * 
 * Database configuration for catalog_product_entity table.
 */
class Resource_Product extends Resource_Abstract
{
    /**
     * @var string
     */
    protected $tableName = 'catalog_product_entity';

    /**
     * @var string
     */
    protected $primaryKey = 'entity_id';

    /**
     * @var array
     */
    protected $columns = [
        'entity_id',
        'sku',
        'url_key',
        'name',
        'price',
        'original_price',
        'stock',
        'description',
        'is_active',
        'is_featured',
        'is_new',
        'rating',
        'reviews_count',
        'created_at',
        'updated_at'
    ];

    /**
     * Attribute table name
     * @var string
     */
    protected $attributeTable = 'catalog_product_attribute';

    /**
     * Image table name
     * @var string
     */
    protected $imageTable = 'catalog_product_image';

    /**
     * Category link table name
     * @var string
     */
    protected $categoryLinkTable = 'catalog_category_product';

    /**
     * Get attribute table name
     * @return string
     */
    public function getAttributeTable(): string
    {
        return $this->attributeTable;
    }

    /**
     * Get image table name
     * @return string
     */
    public function getImageTable(): string
    {
        return $this->imageTable;
    }

    /**
     * Get category link table name
     * @return string
     */
    public function getCategoryLinkTable(): string
    {
        return $this->categoryLinkTable;
    }
}
