<?php

class Admin_Block_Catalog_Product_Index extends Core_Block_Template
{
     public function _contstruct()
     {

     }
     public function __construct()
     {
          parent::__construct();
          $this->setTemplate("Admin\View\Catalog\Product\index.phtml");
     }
}
?>