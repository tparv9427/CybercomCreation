<?php
class Page_Block_Header_Menu extends Core_Block_Template
{
    public function __construct()
    {
        $this->setTemplate("Page/View/Header/menu.phtml");
    }

    public function getNavLinks()
    {
        $base = Sdp::getModel("core/request")->getBaseUrl();
        $homeUrl = $base;
        $productUrl = $base . "catalog/product/list";
        return [
            $homeUrl => "Home",
            $productUrl => "Products",
            $base . "cart" => "Cart",
            $base . "profile" => "Profile",
        ];
    }
}
