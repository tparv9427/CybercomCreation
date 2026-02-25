<?php

class Admin_Block_Catalog_Category_Index extends Core_Block_Template
{
     public function _contstruct()
     {

     }
     public function __construct()
     {
          parent::__construct();
          $this->setTemplate("Admin\View\Catalog\Category\index.phtml");
     }
}
?>