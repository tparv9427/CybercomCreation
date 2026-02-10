<?php

namespace EasyCart\View;

/**
 * View_Dashboard — User Dashboard Page
 */
class View_Dashboard extends View_Abstract
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->template = __DIR__ . '/Templates/dashboard/index.php';
    }
}
