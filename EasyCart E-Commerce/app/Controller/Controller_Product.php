<?php

namespace EasyCart\Controller;

use EasyCart\Collection\Collection_Product;
use EasyCart\Collection\Collection_Category;
use EasyCart\Resource\Resource_Brand;
use EasyCart\View\View_Product_Index;
use EasyCart\View\View_Product_Detail;
use EasyCart\View\View_Search;
use EasyCart\View\View_Brand;

/**
 * Controller_Product — Product Listing, Detail, Search, Brand
 * 
 * No SQL, no HTML. Uses Collection + View classes.
 */
class Controller_Product extends Controller_Abstract
{
    private $productCollection;
    private $categoryCollection;
    private $brandResource;
    private $urlRewriteService;

    public function __construct()
    {
        $this->productCollection = new Collection_Product();
        $this->categoryCollection = new Collection_Category();
        $this->brandResource = new Resource_Brand();
        $this->urlRewriteService = new \EasyCart\Services\UrlRewriteService();
    }

    /**
     * Product listing page
     */
    public function index(): void
    {
        $category_id = isset($_GET['category']) ? (int) $_GET['category'] : null;
        $brand_id = isset($_GET['brand']) ? (int) $_GET['brand'] : null;
        $price_range = $_GET['price'] ?? null;
        $rating_filter = isset($_GET['rating']) ? (float) $_GET['rating'] : null;
        $show_new = isset($_GET['new']);

        if ($category_id) {
            $filtered_products = $this->productCollection->findByCategory($category_id);
            $catData = $this->categoryCollection->findById($category_id);
            $page_title = ($catData ? $catData['name'] : 'Category') . ' Products';
        } elseif ($show_new) {
            $filtered_products = $this->productCollection->getNew();
            $page_title = 'New Arrivals';
        } else {
            $filtered_products = $this->productCollection->getAll();
            $page_title = 'All Products';
        }

        if ($price_range) {
            $filtered_products = $this->productCollection->filterByPrice($filtered_products, $price_range);
        }
        if ($rating_filter) {
            $filtered_products = $this->productCollection->filterByRating($filtered_products, $rating_filter);
        }

        // Sorting
        $sort = $_GET['sort'] ?? 'newest';
        usort($filtered_products, function ($a, $b) use ($sort) {
            switch ($sort) {
                case 'price_low':
                    return $a['price'] <=> $b['price'];
                case 'price_high':
                    return $b['price'] <=> $a['price'];
                case 'rating':
                    return $b['rating'] <=> $a['rating'];
                default:
                    return $b['id'] <=> $a['id'];
            }
        });

        // Pagination
        $total_products = count($filtered_products);
        $limit = 24;
        $total_pages = ceil($total_products / $limit);
        $current_page = max(1, min((int) ($_GET['page'] ?? 1), $total_pages > 0 ? $total_pages : 1));
        $offset = ($current_page - 1) * $limit;
        $filtered_products = array_slice($filtered_products, $offset, $limit);

        $categories = $this->categoryCollection->getAll();
        $brands = $this->brandResource->getAllBrands();

        $contentView = new View_Product_Index([
            'filtered_products' => $filtered_products,
            'categories' => $categories,
            'brands' => $brands,
            'product_count' => $total_products,
            'current_page' => $current_page,
            'total_pages' => $total_pages,
            'offset' => $offset,
            'limit' => $limit,
            'page_title' => $page_title,
            'category_id' => $category_id,
            'brand_id' => $brand_id,
            'price_range' => $price_range,
            'rating_filter' => $rating_filter,
            'sort' => $sort,
        ]);

        $this->renderWithLayout($contentView, [
            'page_title' => $page_title,
            'categories' => $categories,
        ]);
    }

    /**
     * Product detail page — by numeric ID (STRICT 301 redirect to slug URL)
     */
    public function show($id): void
    {
        $product = $this->productCollection->findById($id);

        if (!$product) {
            $this->redirect('/products');
        }

        // Always 301 redirect to the new canonical /p/slug format
        $this->redirect($this->urlRewriteService->getProductUrl($product), 301);
    }

    /**
     * Product detail page — by URL slug
     */
    public function showBySlug($slug): void
    {
        // 1. Resolve slug using UrlRewrite system
        $rewrite = $this->urlRewriteService->resolve('p/' . $slug);

        if (!$rewrite) {
            // Check if it's a legacy /product/slug that needs redirecting
            // The router already handles /product/{slug} -> showBySlug
            // We just need to check if the current URI is /product/...
            $uri = $_SERVER['REQUEST_URI'] ?? '';
            if (strpos($uri, '/product/') === 0) {
                // It's a legacy slug, find the product and redirect to /p/slug
                $product = $this->productCollection->findByUrlKey($slug);
                if ($product) {
                    $this->redirect($this->urlRewriteService->getProductUrl($product), 301);
                }
            }
            $this->redirect('/products');
        }

        // 2. Handle Redirects (e.g., if slug changed and this is an old one)
        if ($rewrite['redirect_type'] == 301) {
            $this->redirect('/' . $rewrite['target_path'], 301);
        }

        // 3. Extract product ID from target_path (e.g., 'product/view/123')
        $parts = explode('/', $rewrite['target_path']);
        $productId = end($parts);

        $product = $this->productCollection->findById($productId);

        if (!$product) {
            $this->redirect('/products');
        }

        $this->renderProductDetail($product);
    }

    /**
     * Shared product detail rendering
     */
    private function renderProductDetail(array $product): void
    {
        $categories = $this->categoryCollection->getAll();
        $brand_recommendations = $this->productCollection->getSimilarByBrand($product);
        $category_recommendations = $this->productCollection->getSimilarByCategory($product);
        $other_recommendations = $this->productCollection->getFromOtherCategories($product);

        $contentView = new View_Product_Detail([
            'product' => $product,
            'brand_recommendations' => $brand_recommendations,
            'category_recommendations' => $category_recommendations,
            'other_recommendations' => $other_recommendations,
            'categories' => $categories,
        ]);

        $this->renderWithLayout($contentView, [
            'page_title' => $product['name'],
            'categories' => $categories,
        ]);
    }

    /**
     * Search products
     */
    public function search(): void
    {
        $query = trim($_GET['q'] ?? '');
        if (empty($query)) {
            $this->redirect('/products');
        }

        $filtered_products = $this->productCollection->search($query);
        $product_count = count($filtered_products);
        $categories = $this->categoryCollection->getAll();

        $contentView = new View_Search([
            'filtered_products' => $filtered_products,
            'product_count' => $product_count,
            'query' => $query,
            'categories' => $categories,
            'page_title' => 'Search Results for "' . $query . '"',
            'category_id' => null,
            'brand_id' => null,
            'price_range' => null,
            'rating_filter' => null,
            'brands' => $this->brandResource->getAllBrands(), // Required for sidebar
        ]);

        $this->renderWithLayout($contentView, [
            'page_title' => 'Search Results',
            'categories' => $categories,
        ]);
    }

    /**
     * Brand page
     */
    public function brand($id): void
    {
        $brand_id = $id ?? (isset($_GET['id']) ? (int) $_GET['id'] : null);
        $brand = $this->brandResource->load($brand_id);

        if (!$brand) {
            $this->redirect('/products');
        }

        $brand['name'] = $brand['attribute_value'];

        $filtered_products = $this->productCollection->findByBrand($brand['attribute_value'] ?? '');
        $categories = $this->categoryCollection->getAll();

        // Pagination
        $total_products = count($filtered_products);
        $limit = 25;
        $total_pages = ceil($total_products / $limit);
        $current_page = max(1, min((int) ($_GET['page'] ?? 1), $total_pages > 0 ? $total_pages : 1));
        $offset = ($current_page - 1) * $limit;
        $filtered_products = array_slice($filtered_products, $offset, $limit);

        $contentView = new View_Brand([
            'filtered_products' => $filtered_products,
            'brand' => $brand,
            'product_count' => $total_products,
            'current_page' => $current_page,
            'total_pages' => $total_pages,
            'offset' => $offset,
            'limit' => $limit,
            'page_title' => ($brand['attribute_value'] ?? 'Brand'),
            'categories' => $categories,
            'brands' => $this->brandResource->getAllBrands(),
            'category_id' => null,
            'brand_id' => $brand_id,
            'price_range' => null,
            'rating_filter' => null,
        ]);

        $this->renderWithLayout($contentView, [
            'page_title' => ($brand['attribute_value'] ?? 'Brand') . ' Products',
            'categories' => $categories,
        ]);
    }
}
