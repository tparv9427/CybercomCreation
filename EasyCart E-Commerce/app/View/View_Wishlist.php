<?php

namespace EasyCart\View;

/**
 * View_Wishlist — Wishlist Page
 */
class View_Wishlist extends View_Abstract
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->template = __DIR__ . '/Templates/wishlist/index.php';
    }
}
