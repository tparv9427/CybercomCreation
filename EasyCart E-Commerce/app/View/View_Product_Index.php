<?php

namespace EasyCart\View;

/**
 * View_Product_Index — Product Listing Page
 */
class View_Product_Index extends View_Abstract
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->template = __DIR__ . '/Templates/products/index.php';
    }
}
