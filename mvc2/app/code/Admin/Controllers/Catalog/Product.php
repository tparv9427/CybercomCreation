<?php

class Admin_Controllers_Catalog_Product
{
    private $model = "admin/";
    private $controller = "catalog_product_";
    public function indexAction()
    {
        $root = Sdp::getBlock("page/root");
        $index = Sdp::getBlock($this->model . $this->controller .'index');
        $root->getChild('content')->addChild('index', $index);
        $root->toHtml();
    }

 public function listAction()
    {
        $root = Sdp::getBlock("page/root");
        $list = Sdp::getBlock($this->model . $this->controller .'list');
        $root->getChild('content')->addChild('list', $list);
        $root->toHtml();
    }

    public function newAction()
    {
        $root = Sdp::getBlock("page/root");
        $new = Sdp::getBlock($this->model . $this->controller .'new');
        $root->getChild('content')->addChild('new', $new);
        $root->toHtml();
    }

    public function editAction()
    {
        $root = Sdp::getBlock("page/root");
        $edit = Sdp::getBlock($this->model . $this->controller .'edit');
        $root->getChild('content')->addChild('edit', $edit);
        $root->toHtml();
    }

    public function deleteAction()
    {
        $root = Sdp::getBlock("page/root");
        $delete = Sdp::getBlock($this->model . $this->controller .'delete');
        $root->getChild('content')->addChild('delete', $delete);
        $root->toHtml();
    }
}
?>