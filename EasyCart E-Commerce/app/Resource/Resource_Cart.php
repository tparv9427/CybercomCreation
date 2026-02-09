<?php

namespace EasyCart\Resource;

/**
 * Resource_Cart
 * 
 * Database configuration for sales_cart table.
 */
class Resource_Cart extends Resource_Abstract
{
    protected $tableName = 'sales_cart';
    protected $primaryKey = 'cart_id';
    protected $columns = [
        'cart_id',
        'user_id',
        'session_id',
        'is_active',
        'created_at',
        'updated_at'
    ];

    protected $productTable = 'sales_cart_product';

    public function getProductTable(): string
    {
        return $this->productTable;
    }
}
