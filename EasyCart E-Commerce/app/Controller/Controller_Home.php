<?php

namespace EasyCart\Controller;

use EasyCart\Collection\Collection_Product;
use EasyCart\Collection\Collection_Category;
use EasyCart\View\View_Home;

/**
 * Controller_Home — Home Page Controller
 * 
 * No SQL, no HTML. Uses Collection + View classes.
 */
class Controller_Home extends Controller_Abstract
{
    private $productCollection;
    private $categoryCollection;

    public function __construct()
    {
        $this->productCollection = new Collection_Product();
        $this->categoryCollection = new Collection_Category();
    }

    /**
     * Display homepage
     */
    public function index(): void
    {
        $featured = $this->productCollection->getFeatured();
        $newProducts = $this->productCollection->getNew();
        $categories = $this->categoryCollection->getAll();

        $getCategory = [\EasyCart\Helpers\ViewHelper::class, 'getCategory'];
        $isInWishlist = [\EasyCart\Helpers\ViewHelper::class, 'isInWishlist'];
        $formatPrice = [\EasyCart\Helpers\ViewHelper::class, 'formatPrice'];

        $contentView = new View_Home([
            'featured' => $featured,
            'newProducts' => $newProducts,
            'categories' => $categories,
            'getCategory' => $getCategory,
            'isInWishlist' => $isInWishlist,
            'formatPrice' => $formatPrice,
        ]);

        $this->renderWithLayout($contentView, [
            'page_title' => 'Home',
            'categories' => $categories,
        ]);
    }
}
