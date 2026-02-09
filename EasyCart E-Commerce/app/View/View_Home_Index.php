<?php

namespace EasyCart\View;

/**
 * View_Home_Index
 * 
 * Renders the homepage.
 */
class View_Home_Index extends View_Abstract
{
    public function toHtml(): string
    {
        $header = (new View_Layout_Header())->setDataArray($this->data)->toHtml();
        $content = $this->renderTemplate('home/index.php');
        $footer = (new View_Layout_Footer())->setDataArray($this->data)->toHtml();

        return $header . $content . $footer;
    }
}
