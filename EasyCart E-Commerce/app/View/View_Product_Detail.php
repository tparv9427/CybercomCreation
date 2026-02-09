<?php

namespace EasyCart\View;

/**
 * View_Product_Detail
 * 
 * Renders the product detail page.
 */
class View_Product_Detail extends View_Abstract
{
    public function toHtml(): string
    {
        $header = (new View_Layout_Header())->setDataArray($this->data)->toHtml();
        $content = $this->renderTemplate('products/detail.php');
        $footer = (new View_Layout_Footer())->setDataArray($this->data)->toHtml();

        return $header . $content . $footer;
    }
}
