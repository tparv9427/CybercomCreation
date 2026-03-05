<?php

class Core_Model_Request
{

    protected $_module = "page";
    protected $_controller = "index";
    protected $_action = "index";

    public function __construct()
    {
        $uri = $this->getRequestUri();

        // Strip query string, then explode path into segments
        $path = explode('?', $uri)[0];
        $path = str_replace($this->getBaseUrl(), "", $path);
        $segments = array_values(array_filter(explode('/', $path)));

        $this->_module = isset($segments[0]) ? $segments[0] : "page";
        $this->_controller = isset($segments[1]) ? $segments[1] : "index";
        $this->_action = isset($segments[2]) ? $segments[2] : "index";
        // Params come from query string: e.g. ?id=1
    }



    public function getRequestUri()
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== "off") ? 'https' : 'http';
        $fullUrl = $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        return $fullUrl;
    }

    public function getParams()
    {
        return $_REQUEST;
    }

    public function isPost()
    {
        return $_SERVER['REQUEST_METHOD'] === 'POST';
    }

    public function getQuery()
    {
        return $_GET;
    }

    public function getPost()
    {
        return $_POST;
    }

    public function getBaseUrl()
    {
        return "http://localhost:" . $_SERVER['SERVER_PORT'] . "/";
    }

    public function getModuleName()
    {
        return $this->_module;
    }

    public function getControllerName()
    {
        return $this->_controller;
    }

    public function getActionName()
    {
        return $this->_action;
    }
}
