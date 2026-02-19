<?php

class Catalog_Block_Product_View extends Core_Block_Template
{
     public function _contstruct()
     {

     }
     public function __construct()
     {
          $this->setTemplate("Catalog/View/Product/view.phtml");
     }

     public function getProduct()
     {
          $product = Sdp::getModel("catalog/product");
          $product->addData(
               [
                    "product_ID" => 1,
                    "name"       => "Dell Laptop 001",
                    "url"        => "Dell-Laptop-001",
               ]
          );
          // echo "<pre>";
          // print_r($product);
          return $product;
     }
}
?>