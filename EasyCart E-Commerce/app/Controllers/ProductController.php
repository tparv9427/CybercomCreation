<?php

namespace EasyCart\Controllers;

use EasyCart\Repositories\ProductRepository;
use EasyCart\Repositories\CategoryRepository;
use EasyCart\Repositories\BrandRepository;
use EasyCart\Services\WishlistService;

/**
 * ProductController
 * 
 * Migrated from: products.php, product.php, search.php, brand.php
 */
class ProductController
{
    private $productRepo;
    private $categoryRepo;
    private $brandRepo;
    private $wishlistService;

    public function __construct()
    {
        $this->productRepo = new ProductRepository();
        $this->categoryRepo = new CategoryRepository();
        $this->brandRepo = new BrandRepository();
        $this->wishlistService = new WishlistService();
    }

    /**
     * Product listing page
     */
    public function index()
    {
        // Get filter parameters
        $category_id = isset($_GET['category']) ? (int) $_GET['category'] : null;
        $brand_id = isset($_GET['brand']) ? (int) $_GET['brand'] : null;
        $price_range = isset($_GET['price']) ? $_GET['price'] : null;
        $rating_filter = isset($_GET['rating']) ? (float) $_GET['rating'] : null;
        $show_new = isset($_GET['new']) ? true : false;

        // Filter products
        $filtered_products = $this->productRepo->getAll();

        if ($category_id) {
            $filtered_products = $this->productRepo->findByCategory($category_id);
            $page_title = $this->categoryRepo->find($category_id)['name'] . ' Products';
        } elseif ($brand_id) {
            $filtered_products = $this->productRepo->findByBrand($brand_id);
            $page_title = $this->brandRepo->find($brand_id)['name'] . ' Products';
        } elseif ($show_new) {
            $filtered_products = $this->productRepo->getNew();
            $page_title = 'New Arrivals';
        } else {
            $page_title = 'All Products';
        }

        // Apply price filter
        if ($price_range) {
            $filtered_products = $this->productRepo->filterByPrice($filtered_products, $price_range);
        }

        // Apply rating filter
        if ($rating_filter) {
            $filtered_products = $this->productRepo->filterByRating($filtered_products, $rating_filter);
        }

        // Apply sorting
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'newest';

        usort($filtered_products, function ($a, $b) use ($sort) {
            switch ($sort) {
                case 'price_low':
                    return $a['price'] <=> $b['price'];
                case 'price_high':
                    return $b['price'] <=> $a['price'];
                case 'rating':
                    return $b['rating'] <=> $a['rating'];
                case 'newest':
                default:
                    // Assuming higher ID is newer for now, or add created_at
                    return $b['id'] <=> $a['id'];
            }
        });

        // Pagination
        $total_products = count($filtered_products);
        $limit = 24;
        $total_pages = ceil($total_products / $limit);
        $current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $current_page = max(1, min($current_page, $total_pages > 0 ? $total_pages : 1));
        $offset = ($current_page - 1) * $limit;

        $filtered_products = array_slice($filtered_products, $offset, $limit);

        $categories = $this->categoryRepo->getAll();
        $brands = $this->brandRepo->getAll(); // Fetch all brands for sidebar

        // Helper functions
        $getCategory = function ($id) {
            return $this->categoryRepo->find($id);
        };

        $isInWishlist = function ($productId) {
            return $this->wishlistService->has($productId);
        };

        $formatPrice = function ($price) {
            return \EasyCart\Helpers\FormatHelper::price($price);
        };

        include __DIR__ . '/../Views/layouts/header.php';
        include __DIR__ . '/../Views/products/index.php';
        include __DIR__ . '/../Views/layouts/footer.php';
    }

    /**
     * Product detail page
     */
    public function show($id)
    {
        $product_id = $id ?? (isset($_GET['id']) ? (int) $_GET['id'] : null);
        $product = $this->productRepo->find($product_id);

        if (!$product) {
            header('Location: products.php');
            exit;
        }

        $page_title = $product['name'];
        $categories = $this->categoryRepo->getAll();

        // Get recommendations
        $brand_recommendations = $this->productRepo->getSimilarByBrand($product);
        $category_recommendations = $this->productRepo->getSimilarByCategory($product);
        $other_recommendations = $this->productRepo->getFromOtherCategories($product);

        // Helper functions
        $getCategory = function ($id) {
            return $this->categoryRepo->find($id);
        };

        $getBrand = function ($id) {
            return $this->brandRepo->find($id);
        };

        $isInWishlist = function ($productId) {
            return $this->wishlistService->has($productId);
        };

        $formatPrice = function ($price) {
            return \EasyCart\Helpers\FormatHelper::price($price);
        };

        include __DIR__ . '/../Views/layouts/header.php';
        include __DIR__ . '/../Views/products/detail.php';
        include __DIR__ . '/../Views/layouts/footer.php';
    }

    /**
     * Search products
     */
    public function search()
    {
        $query = isset($_GET['q']) ? trim($_GET['q']) : '';
        $page_title = 'Search Results';

        if (empty($query)) {
            header('Location: products.php');
            exit;
        }

        $products = $this->productRepo->getAll();
        $filtered_products = array_filter($products, function ($product) use ($query) {
            return stripos($product['name'], $query) !== false ||
                stripos($product['description'], $query) !== false;
        });

        $product_count = count($filtered_products);
        $categories = $this->categoryRepo->getAll();

        include __DIR__ . '/../Views/layouts/header.php';
        include __DIR__ . '/../Views/search/index.php';
        include __DIR__ . '/../Views/layouts/footer.php';
    }

    /**
     * Brand page
     */
    public function brand($id)
    {
        $brand_id = $id ?? (isset($_GET['id']) ? (int) $_GET['id'] : null);
        $brand = $this->brandRepo->find($brand_id);

        if (!$brand) {
            header('Location: products.php');
            exit;
        }

        $page_title = $brand['name'] . ' Products';
        $filtered_products = $this->productRepo->findByBrand($brand_id);

        // Pagination
        $total_products = count($filtered_products);
        $limit = 25;
        $total_pages = ceil($total_products / $limit);
        $current_page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
        $current_page = max(1, min($current_page, $total_pages > 0 ? $total_pages : 1));
        $offset = ($current_page - 1) * $limit;

        $filtered_products = array_slice($filtered_products, $offset, $limit);

        $product_count = count($filtered_products);
        $categories = $this->categoryRepo->getAll();

        // Helper functions for views
        $getCategory = function ($id) {
            return $this->categoryRepo->find($id);
        };

        $isInWishlist = function ($productId) {
            return $this->wishlistService->has($productId);
        };

        $formatPrice = function ($price) {
            return \EasyCart\Helpers\FormatHelper::price($price);
        };

        include __DIR__ . '/../Views/layouts/header.php';
        include __DIR__ . '/../Views/brand/index.php';
        include __DIR__ . '/../Views/layouts/footer.php';
    }
}
