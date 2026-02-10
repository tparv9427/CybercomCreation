<?php

namespace EasyCart\View;

/**
 * View_Brand — Brand Products Page
 */
class View_Brand extends View_Abstract
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->template = __DIR__ . '/Templates/brand/index.php';
    }
}
