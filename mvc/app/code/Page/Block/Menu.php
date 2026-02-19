<?php

class Page_Block_Menu extends Core_Block_Template
{
    public function __construct()
    {
        $this->setTemplate("Page/View/menu.phtml");
    }

    public function getMenuArray()
    {
        $category = [
            "url1" => "category1",
            "url2" => "category2",
            "url3" => "category3"
        ];        
        return $category;
    }
}
?>