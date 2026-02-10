<?php

namespace EasyCart\View;

/**
 * View_Checkout — Checkout Page
 */
class View_Checkout extends View_Abstract
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->template = __DIR__ . '/Templates/checkout/index.php';
    }
}
