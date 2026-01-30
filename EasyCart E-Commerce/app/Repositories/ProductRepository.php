<?php

namespace EasyCart\Repositories;

/**
 * ProductRepository
 * 
 * Migrated from: includes/config.php
 */
class ProductRepository
{
    private $products = [];
    private $productsFile;

    public function __construct()
    {
        $this->productsFile = __DIR__ . '/../../data/products.json';
        $this->loadProducts();
    }

    private function loadProducts()
    {
        if (file_exists($this->productsFile)) {
            $json = file_get_contents($this->productsFile);
            $this->products = json_decode($json, true);
            // $this->dynamicDiscounts();
        } else {
            error_log("Products file not found: {$this->productsFile}");
            $this->products = [];
        }
    }

    private function dynamicDiscounts()
    {
        foreach ($this->products as $id => &$product) {
            $originalPrice = $product['original_price'] ?? $product['price'];
            $discount = 0;
            $discount = $originalPrice > 900 ? 90 : (
                $originalPrice > 800 ? 80 : (
                    $originalPrice > 700 ? 70 : (
                        $originalPrice > 600 ? 60 : (
                            $originalPrice > 500 ? 50 : (
                                $originalPrice > 400 ? 40 : (
                                    $originalPrice > 300 ? 30 : (
                                        $originalPrice > 200 ? 20 : (
                                            $originalPrice >= 100 ? 10 : 0
                                        ))))))));
            $product['original_price'] = $originalPrice;
            $product['discount'] = $discount;
            $product['discount_percent'] = $discount;
            $product['price'] = round($originalPrice * (1 - $discount / 100), 2);

            // Mock "bought in past month" data
            // Deterministic random based on ID so it stays constant for the same product
            srand($product['id']);
            $sales = rand(5, 50) * 10; // 50, 60... 500
            if (rand(0, 10) > 8)
                $sales = rand(1, 9) . 'k'; // Occasional "1k+"
            else
                $sales .= '+';

            $product['bought_past_month'] = $sales;

            // Mock Trust Badges
            // Pool of badges: id => [label, icon_char] (using chars/emoji for now as placeholder for SVGs in view)
            $allBadges = ['free_delivery', 'replacement', 'warranty', 'top_brand', 'secure', 'easycart_delivered'];

            // Pick 3-5 random badges
            $count = rand(3, 5);
            $keys = array_rand($allBadges, $count);
            if (!is_array($keys))
                $keys = [$keys];

            $product['trust_badges'] = [];
            foreach ($keys as $key) {
                $product['trust_badges'][] = $allBadges[$key];
            }

            srand(); // Reset
        }
    }

    public function getAll()
    {
        return $this->products;
    }

    public function find($id)
    {
        return isset($this->products[$id]) ? $this->products[$id] : null;
    }

    public function getFeatured($limit = 20)
    {
        $featured = array_filter($this->products, function ($product) {
            return $product['featured'] === true;
        });
        return array_slice($featured, 0, $limit);
    }

    public function getNew($limit = 6)
    {
        $new = array_filter($this->products, function ($product) {
            return $product['new'] === true;
        });
        return array_slice($new, 0, $limit);
    }

    public function findByCategory($categoryId)
    {
        return array_filter($this->products, function ($product) use ($categoryId) {
            return $product['category_id'] == $categoryId;
        });
    }

    public function findByBrand($brandId)
    {
        return array_filter($this->products, function ($product) use ($brandId) {
            return $product['brand_id'] == $brandId;
        });
    }

    public function filterByPrice($products, $priceRange)
    {
        return array_filter($products, function ($product) use ($priceRange) {
            switch ($priceRange) {
                case 'under50':
                    return $product['price'] < 50;
                case '50-100':
                    return $product['price'] >= 50 && $product['price'] <= 100;
                case '100-200':
                    return $product['price'] > 100 && $product['price'] <= 200;
                case '200plus':
                    return $product['price'] > 200;
                default:
                    return true;
            }
        });
    }

    public function filterByRating($products, $rating)
    {
        return array_filter($products, function ($product) use ($rating) {
            return $product['rating'] >= $rating;
        });
    }

    public function getSimilarByBrand($currentProduct, $limit = 4)
    {
        $recommendations = array_filter($this->products, function ($p) use ($currentProduct) {
            return $p['id'] != $currentProduct['id'] &&
                $p['category_id'] == $currentProduct['category_id'] &&
                $p['brand_id'] != $currentProduct['brand_id'];
        });
        return array_slice($recommendations, 0, $limit);
    }

    public function getSimilarByCategory($currentProduct, $limit = 4)
    {
        $recommendations = array_filter($this->products, function ($p) use ($currentProduct) {
            return $p['id'] != $currentProduct['id'] &&
                $p['category_id'] == $currentProduct['category_id'];
        });
        return array_slice($recommendations, 0, $limit);
    }

    public function getFromOtherCategories($currentProduct, $limit = 4)
    {
        $recommendations = array_filter($this->products, function ($p) use ($currentProduct) {
            return $p['id'] != $currentProduct['id'] &&
                $p['category_id'] != $currentProduct['category_id'];
        });
        return array_slice($recommendations, 0, $limit);
    }
}
