<?php

class Admin_Block_Customer_Delete extends Core_Block_Template{
    public function _contstruct(){

    }
   public function __construct(){

        $this->setTemplate("Admin\View\Customer\delete.phtml");
         $this->addJs("js/default.js")
         ->addJs("js/default1.js");
   }
}
?>