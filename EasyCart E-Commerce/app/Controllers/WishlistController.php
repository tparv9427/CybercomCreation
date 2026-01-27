<?php

namespace EasyCart\Controllers;

use EasyCart\Services\WishlistService;
use EasyCart\Repositories\ProductRepository;

/**
 * WishlistController
 * 
 * Migrated from: wishlist.php, ajax_wishlist.php
 */
class WishlistController
{
    private $wishlistService;
    private $productRepo;

    public function __construct()
    {
        $this->wishlistService = new WishlistService();
        $this->productRepo = new ProductRepository();
    }

    /**
     * Wishlist page
     */
    public function index()
    {
        $page_title = 'My Wishlist';
        
        $wishlist = $this->wishlistService->get();
        $wishlist_items = [];
        
        foreach ($wishlist as $product_id) {
            $product = $this->productRepo->find($product_id);
            if ($product) {
                $wishlist_items[] = $product;
            }
        }

        $formatPrice = function($price) {
            return \EasyCart\Helpers\FormatHelper::price($price);
        };

        include __DIR__ . '/../Views/layouts/header.php';
        include __DIR__ . '/../Views/wishlist/index.php';
        include __DIR__ . '/../Views/layouts/footer.php';
    }

    /**
     * Toggle wishlist (AJAX)
     */
    public function toggle()
    {
        header('Content-Type: application/json');
        
        $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

        $added = $this->wishlistService->toggle($product_id);

        echo json_encode([
            'success' => true,
            'added' => $added,
            'wishlist_count' => $this->wishlistService->getCount(),
            'message' => $added ? 'Added to wishlist' : 'Removed from wishlist'
        ]);
    }
}
