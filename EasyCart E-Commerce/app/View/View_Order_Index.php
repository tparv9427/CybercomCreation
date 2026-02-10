<?php

namespace EasyCart\View;

/**
 * View_Order_Index — Orders List Page
 */
class View_Order_Index extends View_Abstract
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->template = __DIR__ . '/Templates/orders/index.php';
    }
}
