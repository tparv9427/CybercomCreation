<?php

namespace EasyCart\Controller;

use EasyCart\Services\WishlistService;
use EasyCart\Services\CartService;
use EasyCart\Collection\Collection_Product;
use EasyCart\Collection\Collection_Category;
use EasyCart\View\View_Wishlist;

/**
 * Controller_Wishlist — Wishlist Page + AJAX
 * 
 * No SQL, no HTML.
 */
class Controller_Wishlist extends Controller_Abstract
{
    private $wishlistService;
    private $cartService;
    private $productCollection;
    private $categoryCollection;

    public function __construct()
    {
        $this->wishlistService = new WishlistService();
        $this->cartService = new CartService();
        $this->productCollection = new Collection_Product();
        $this->categoryCollection = new Collection_Category();
    }

    public function index(): void
    {
        $categories = $this->categoryCollection->getAll();
        $wishlist = $this->wishlistService->get();
        $wishlist_items = [];

        foreach ($wishlist as $product_id) {
            $product = $this->productCollection->findById($product_id);
            if ($product) {
                $wishlist_items[] = $product;
            }
        }

        $contentView = new View_Wishlist([
            'wishlist_items' => $wishlist_items,
            'categories' => $categories,
            'formatPrice' => [\EasyCart\Helpers\ViewHelper::class, 'formatPrice'],
            'getCategory' => [\EasyCart\Helpers\ViewHelper::class, 'getCategory'],
        ]);

        $this->renderWithLayout($contentView, [
            'page_title' => 'My Wishlist',
            'categories' => $categories,
        ]);
    }

    public function toggle(): void
    {
        $product_id = (int) ($_POST['product_id'] ?? 0);
        $added = $this->wishlistService->toggle($product_id);

        $this->jsonResponse([
            'success' => true,
            'in_wishlist' => $added,
            'wishlist_count' => $this->wishlistService->getCount(),
            'message' => $added ? 'Added to wishlist' : 'Removed from wishlist'
        ]);
    }

    public function moveToCart(): void
    {
        $product_id = (int) ($_POST['product_id'] ?? 0);
        $this->cartService->add($product_id, 1);

        if ($this->wishlistService->has($product_id)) {
            $this->wishlistService->remove($product_id);
        }

        $this->jsonResponse([
            'success' => true,
            'cart_count' => $this->cartService->getCount(),
            'wishlist_count' => $this->wishlistService->getCount(),
            'message' => 'Moved to cart successfully'
        ]);
    }

    public function count(): void
    {
        $this->jsonResponse(['success' => true, 'wishlist_count' => $this->wishlistService->getCount()]);
    }
}
