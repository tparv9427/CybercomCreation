<?php

namespace EasyCart\View;

/**
 * View_Search — Search Results Page
 */
class View_Search extends View_Abstract
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        // Check if search view exists, fallback to product index
        $searchTemplate = __DIR__ . '/Templates/search/index.php';
        $this->template = file_exists($searchTemplate) ? $searchTemplate : __DIR__ . '/Templates/products/index.php';
    }
}
