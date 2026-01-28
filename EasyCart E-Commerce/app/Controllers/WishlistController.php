<?php

namespace EasyCart\Controllers;

use EasyCart\Services\WishlistService;
use EasyCart\Services\CartService;
use EasyCart\Repositories\ProductRepository;
use EasyCart\Repositories\CategoryRepository;

/**
 * WishlistController
 * 
 * Migrated from: wishlist.php, ajax_wishlist.php
 */
class WishlistController
{
    private $wishlistService;
    private $cartService;
    private $productRepo;
    private $categoryRepo;

    public function __construct()
    {
        $this->wishlistService = new WishlistService();
        $this->cartService = new CartService();
        $this->productRepo = new ProductRepository();
        $this->categoryRepo = new CategoryRepository();
    }

    /**
     * Wishlist page
     */
    public function index()
    {
        $page_title = 'My Wishlist';
        $categories = $this->categoryRepo->getAll();
        
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

        $getCategory = function($id) {
            return $this->categoryRepo->find($id);
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
            'in_wishlist' => $added,
            'wishlist_count' => $this->wishlistService->getCount(),
            'message' => $added ? 'Added to wishlist' : 'Removed from wishlist'
        ]);
    }

    /**
     * Move item from wishlist to cart (AJAX)
     */
    public function moveToCart()
    {
        header('Content-Type: application/json');
        
        $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;

        // Add to cart
        $cartResult = $this->cartService->add($product_id, 1);
        
        // Remove from wishlist
        if ($this->wishlistService->has($product_id)) {
            $this->wishlistService->remove($product_id);
        }

        echo json_encode([
            'success' => true,
            'cart_count' => $this->cartService->getCount(),
            'wishlist_count' => $this->wishlistService->getCount(),
            'message' => 'Moved to cart successfully'
        ]);
    }
}
