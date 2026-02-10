<?php

namespace EasyCart\Resource;

/**
 * Resource_Category — Category DB Configuration
 * 
 * Table: catalog_category_entity
 * Primary Key: entity_id
 */
class Resource_Category extends Resource_Abstract
{
    protected $table = 'catalog_category_entity';
    protected $primaryKey = 'entity_id';
    protected $columns = [
        'entity_id',
        'name',
        'parent_id',
        'position',
        'is_active',
        'created_at',
        'updated_at'
    ];
    /**
     * Get all active categories
     * @return array
     */
    public function getAllCategories(): array
    {
        return \EasyCart\Database\QueryBuilder::select($this->table, ['entity_id as id', 'name'])
            ->where('is_active', '=', true)
            ->orderBy('name', 'ASC')
            ->fetchAll();
    }
}
