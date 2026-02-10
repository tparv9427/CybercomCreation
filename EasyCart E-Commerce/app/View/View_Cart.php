<?php

namespace EasyCart\View;

/**
 * View_Cart — Cart Page
 */
class View_Cart extends View_Abstract
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->template = __DIR__ . '/Templates/cart/index.php';
    }
}
