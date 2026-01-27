<?php

namespace EasyCart\Controllers;

use EasyCart\Repositories\ProductRepository;
use EasyCart\Repositories\CategoryRepository;
use EasyCart\Services\WishlistService;

/**
 * HomeController
 * 
 * Migrated from: index.php
 */
class HomeController
{
    private $productRepo;
    private $categoryRepo;
    private $wishlistService;

    public function __construct()
    {
        $this->productRepo = new ProductRepository();
        $this->categoryRepo = new CategoryRepository();
        $this->wishlistService = new WishlistService();
    }

    /**
     * Display homepage
     */
    public function index()
    {
        $page_title = 'Home';

        // Get data
        $featured = $this->productRepo->getFeatured();
        $newProducts = $this->productRepo->getNew();
        $categories = $this->categoryRepo->getAll();

        // Helper functions for backward compatibility
        $getCategory = function($id) {
            return $this->categoryRepo->find($id);
        };

        $isInWishlist = function($productId) {
            return $this->wishlistService->has($productId);
        };

        $formatPrice = function($price) {
            return \EasyCart\Helpers\FormatHelper::price($price);
        };

        // Include header
        include __DIR__ . '/../Views/layouts/header.php';

        // Include homepage view
        include __DIR__ . '/../Views/home/index.php';

        // Include footer
        include __DIR__ . '/../Views/layouts/footer.php';
    }
}
