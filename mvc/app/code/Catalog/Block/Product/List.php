<?php

class Catalog_Block_Product_List extends Core_Block_Template
{
    protected $request;
    protected $base;

    public function __construct()
    {
        parent::__construct();
        $this->setTemplate("Catalog/View/Product/list.phtml");
        $this->request = Sdp::getModel("core/request");
        $this->base = $this->request->getBaseUrl();
    }

    public function _construct()
    {
    }

    public function getProducts()
    {
        $product = Sdp::getModel("catalog/product");
        return $product->getResource()->fetchAll();
    }
}
