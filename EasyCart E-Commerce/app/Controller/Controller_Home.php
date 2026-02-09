<?php

namespace EasyCart\Controller;

use EasyCart\Collection\Collection_Product;
use EasyCart\Collection\Collection_Category;
use EasyCart\View\View_Home_Index;

/**
 * Controller_Home
 * 
 * Handles the homepage.
 */
class Controller_Home
{
    public function index()
    {
        $featuredProducts = (new Collection_Product())
            ->addFeaturedFilter()
            ->setLimit(8)
            ->load()
            ->getItems();

        $newProducts = (new Collection_Product())
            ->addNewFilter()
            ->setLimit(6)
            ->load()
            ->getItems();

        $categories = (new Collection_Category())
            ->addActiveFilter()
            ->setPositionOrder()
            ->load()
            ->getItems();

        $view = new View_Home_Index();
        $view->setDataArray([
            'page_title' => 'EasyCart - Home',
            'featured_products' => $featuredProducts,
            'new_products' => $newProducts,
            'categories' => $categories
        ]);

        echo $view->toHtml();
    }
}
