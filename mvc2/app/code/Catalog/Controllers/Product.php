<?php
class Catalog_Controllers_Product
{   
    private $model = "catalog/";
    private $controller = "product_";
    public function listAction()
    {
        $root = Sdp::getBlock("page/root");
        $list = Sdp::getBlock($this->model . $this->controller .'List');
        $root->getChild('content')->addChild('list',$list);
        $root->toHtml();
    }
    public function viewAction()
    {
        $root = Sdp::getBlock("page/root");
        $view = Sdp::getBlock($this->model . $this->controller .'View');
        $root->getChild('content')->addChild('view',$view);
        $root->toHtml();
    }
}
?>