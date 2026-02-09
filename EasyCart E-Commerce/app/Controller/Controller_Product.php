<?php

namespace EasyCart\Controller;

use EasyCart\Collection\Collection_Product;
use EasyCart\Collection\Collection_Category;
use EasyCart\View\View_Product_Index;
use EasyCart\View\View_Product_Detail;

/**
 * Controller_Product
 * 
 * Handles product listing and detail pages.
 */
class Controller_Product
{
    /**
     * Product listing page
     */
    public function index()
    {
        $collection = new Collection_Product();

        // Apply filters from request
        $categoryId = isset($_GET['category']) ? (int) $_GET['category'] : null;
        $brandName = isset($_GET['brand']) ? $_GET['brand'] : null;
        $showNew = isset($_GET['new']);
        $sort = $_GET['sort'] ?? 'newest';
        $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $limit = 24;

        if ($categoryId) {
            $collection->addCategoryFilter($categoryId);
        }
        if ($brandName) {
            $collection->addBrandFilter($brandName);
        }
        if ($showNew) {
            $collection->addNewFilter();
        }

        // Apply sorting
        switch ($sort) {
            case 'price_low':
                $collection->setPriceLowToHigh();
                break;
            case 'price_high':
                $collection->setPriceHighToLow();
                break;
            case 'rating':
                $collection->setRatingOrder();
                break;
            default:
                $collection->setNewestFirst();
        }

        $collection->setLimit($limit)->setOffset(($page - 1) * $limit)->load();

        // Get categories for sidebar
        $categories = (new Collection_Category())
            ->addActiveFilter()
            ->setPositionOrder()
            ->load()
            ->getItems();

        $view = new View_Product_Index();
        $view->setDataArray([
            'page_title' => 'All Products',
            'filtered_products' => $collection->getItems(),
            'product_count' => $collection->count(),
            'categories' => $categories,
            'current_page' => $page,
            'total_pages' => 1 // Calculate if needed
        ]);

        echo $view->toHtml();
    }

    /**
     * Product detail page
     */
    public function show($id)
    {
        $collection = new Collection_Product();
        $collection->addFilter('p.entity_id', $id)->setLimit(1)->load();
        $product = $collection->getFirstItem();

        if (!$product) {
            header('Location: /products');
            exit;
        }

        $categories = (new Collection_Category())
            ->addActiveFilter()
            ->setPositionOrder()
            ->load()
            ->getItems();

        $view = new View_Product_Detail();
        $view->setDataArray([
            'page_title' => $product['name'],
            'product' => $product,
            'categories' => $categories
        ]);

        echo $view->toHtml();
    }

    /**
     * Product detail page by URL-key (slug)
     */
    public function showBySlug($slug)
    {
        $collection = new Collection_Product();
        $collection->addFilter('p.url_key', $slug)->setLimit(1)->load();
        $product = $collection->getFirstItem();

        if (!$product) {
            header('Location: /products');
            exit;
        }

        $categories = (new Collection_Category())
            ->addActiveFilter()
            ->setPositionOrder()
            ->load()
            ->getItems();

        $view = new View_Product_Detail();
        $view->setDataArray([
            'page_title' => $product['name'],
            'product' => $product,
            'categories' => $categories
        ]);

        echo $view->toHtml();
    }
}
