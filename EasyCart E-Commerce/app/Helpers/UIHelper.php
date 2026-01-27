<?php

namespace EasyCart\Helpers;

/**
 * UIHelper
 * 
 * Extracted from inline logic in index.php, products.php, product.php
 */
class UIHelper
{
    /**
     * Render star rating HTML
     * 
     * @param float $rating Rating from 0-5
     * @return void (echoes HTML)
     */
    public static function renderStarRating($rating)
    {
        $fullStars = floor($rating);
        $halfStar = ($rating - $fullStars) >= 0.5;
        
        for ($i = 0; $i < $fullStars; $i++) {
            echo '★';
        }
        
        if ($halfStar) {
            echo '☆';
        }
        
        for ($i = ceil($rating); $i < 5; $i++) {
            echo '☆';
        }
    }

    /**
     * Render product badge (discount or new)
     * 
     * @param array $product
     * @return void (echoes HTML)
     */
    public static function renderProductBadge($product)
    {
        if ($product['discount_percent'] > 0) {
            echo '<span class="product-badge">-' . $product['discount_percent'] . '%</span>';
        } elseif ($product['new']) {
            echo '<span class="product-badge new">New</span>';
        }
    }

    /**
     * Render breadcrumb navigation
     * 
     * @param array $items Array of ['label' => 'Home', 'url' => '/']
     * @return void (echoes HTML)
     */
    public static function renderBreadcrumb($items)
    {
        echo '<div class="breadcrumb">';
        $lastIndex = count($items) - 1;
        
        foreach ($items as $index => $item) {
            if ($index < $lastIndex) {
                echo '<a href="' . $item['url'] . '">' . $item['label'] . '</a> / ';
            } else {
                echo $item['label'];
            }
        }
        
        echo '</div>';
    }
}
