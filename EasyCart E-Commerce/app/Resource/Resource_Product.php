<?php

namespace EasyCart\Resource;

/**
 * Resource_Product — Product DB Configuration
 * 
 * Table: catalog_product_entity
 * Primary Key: entity_id
 */
class Resource_Product extends Resource_Abstract
{
    protected $table = 'catalog_product_entity';
    protected $primaryKey = 'entity_id';
    protected $columns = [
        'entity_id',
        'name',
        'description',
        'long_description',
        'price',
        'original_price',
        'discount_percent',
        'stock',
        'sku',
        'icon',
        'image',
        'images',
        'rating',
        'reviews_count',
        'is_featured',
        'is_new',
        'is_active',
        'features',
        'specifications',
        'variants',
        'url_key',
        'created_at',
        'updated_at'
    ];
}
