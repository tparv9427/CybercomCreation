<?php

namespace EasyCart\View;

/**
 * View_Layout_Header
 * 
 * Renders the site header.
 */
class View_Layout_Header extends View_Abstract
{
    public function toHtml(): string
    {
        return $this->renderTemplate('layouts/header.php');
    }
}
