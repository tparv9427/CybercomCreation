<?php

namespace EasyCart\View;

/**
 * View_Home — Home Page
 */
class View_Home extends View_Abstract
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->template = __DIR__ . '/Templates/home/index.php';
    }
}
