<?php

namespace EasyCart\Resource;

/**
 * Resource_Brand — Brand DB Configuration
 * 
 * Brands are stored as product attributes in catalog_product_attribute
 */
class Resource_Brand extends Resource_Abstract
{
    protected $table = 'catalog_product_attribute';
    protected $primaryKey = 'attribute_id';
    protected $columns = [
        'attribute_id',
        'product_entity_id',
        'attribute_code',
        'attribute_value'
    ];

    /**
     * Find all unique brand names
     * @return array
     */
    public function getAllBrands(): array
    {
        return \EasyCart\Database\QueryBuilder::select($this->table, ['DISTINCT attribute_value as name', 'MIN(attribute_id) as id'])
            ->where('attribute_code', '=', 'brand')
            ->groupBy('attribute_value')
            ->orderBy('attribute_value', 'ASC')
            ->fetchAll();
    }
}
