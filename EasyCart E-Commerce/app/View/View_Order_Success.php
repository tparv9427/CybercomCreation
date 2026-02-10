<?php

namespace EasyCart\View;

/**
 * View_Order_Success — Order Confirmation Page
 */
class View_Order_Success extends View_Abstract
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->template = __DIR__ . '/Templates/orders/success.php';
    }
}
