<?php

namespace EasyCart\View;

/**
 * View_Layout_Footer
 * 
 * Renders the site footer.
 */
class View_Layout_Footer extends View_Abstract
{
    public function toHtml(): string
    {
        return $this->renderTemplate('layouts/footer.php');
    }
}
