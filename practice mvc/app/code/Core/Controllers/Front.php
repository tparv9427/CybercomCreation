<?php
class Core_Controllers_Front extends Core_Controllers_Front_Action{
    public function run(){
        $request = $this->getRequest();
        $className = sprintf(
            "%s_controllers_%s",
            ucfirst($request->getModuleName()),
            ucfirst($request->getControllerName())
        );
        $action = $request->getActionName() . "Action";
        $classObj = new $className();
        $classObj->$action();


    }
}
?>