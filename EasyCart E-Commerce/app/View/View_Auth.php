<?php

namespace EasyCart\View;

/**
 * View_Auth — Login / Signup Combined Page
 */
class View_Auth extends View_Abstract
{
    public function __construct(array $data = [])
    {
        parent::__construct($data);
        $this->template = __DIR__ . '/Templates/auth/auth_combined.php';
    }
}
