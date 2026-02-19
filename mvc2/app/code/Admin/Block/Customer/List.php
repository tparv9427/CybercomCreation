<?php

class Admin_Block_Customer_List extends Core_Block_Template{
    public function _contstruct(){

    }
   public function __construct(){
        $this->setTemplate("Admin\View\Customer\list.phtml");
   }
}
?>