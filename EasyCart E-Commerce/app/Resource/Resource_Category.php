<?php

namespace EasyCart\Resource;

/**
 * Resource_Category
 * 
 * Database configuration for catalog_category_entity table.
 */
class Resource_Category extends Resource_Abstract
{
    protected $tableName = 'catalog_category_entity';
    protected $primaryKey = 'entity_id';
    protected $columns = [
        'entity_id',
        'name',
        'url_key',
        'description',
        'position',
        'is_active',
        'created_at',
        'updated_at'
    ];
}
