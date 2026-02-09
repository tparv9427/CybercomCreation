<?php

namespace EasyCart\Resource;

/**
 * Resource_Order
 * 
 * Database configuration for sales_order table.
 */
class Resource_Order extends Resource_Abstract
{
    protected $tableName = 'sales_order';
    protected $primaryKey = 'order_id';
    protected $columns = [
        'order_id',
        'order_number',
        'user_id',
        'original_cart_id',
        'subtotal',
        'shipping_cost',
        'tax',
        'discount',
        'total',
        'status',
        'is_archived',
        'created_at',
        'updated_at'
    ];

    protected $productTable = 'sales_order_product';
    protected $addressTable = 'sales_order_address';
    protected $paymentTable = 'sales_order_payment';

    public function getProductTable(): string
    {
        return $this->productTable;
    }

    public function getAddressTable(): string
    {
        return $this->addressTable;
    }

    public function getPaymentTable(): string
    {
        return $this->paymentTable;
    }
}
