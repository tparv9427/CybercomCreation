<?php

namespace EasyCart\View;

/**
 * View_Cart_Index
 * 
 * Renders the shopping cart page.
 */
class View_Cart_Index extends View_Abstract
{
    public function toHtml(): string
    {
        $header = (new View_Layout_Header())->setDataArray($this->data)->toHtml();
        $content = $this->renderTemplate('cart/index.php');
        $footer = (new View_Layout_Footer())->setDataArray($this->data)->toHtml();

        return $header . $content . $footer;
    }
}
