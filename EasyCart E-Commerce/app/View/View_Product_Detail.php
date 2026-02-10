<?php

namespace EasyCart\View;

/**
 * View_Product_Detail — Single Product Detail Page
 */
class View_Product_Detail extends View_Abstract
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->template = __DIR__ . '/Templates/products/detail.php';
    }
}
