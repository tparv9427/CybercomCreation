<?php

namespace EasyCart\View;

/**
 * View_Product_Index
 * 
 * Renders the product listing page.
 */
class View_Product_Index extends View_Abstract
{
    public function toHtml(): string
    {
        $header = (new View_Layout_Header())->setDataArray($this->data)->toHtml();
        $content = $this->renderTemplate('products/index.php');
        $footer = (new View_Layout_Footer())->setDataArray($this->data)->toHtml();

        return $header . $content . $footer;
    }
}
