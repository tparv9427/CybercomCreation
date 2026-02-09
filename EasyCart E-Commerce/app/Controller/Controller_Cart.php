<?php

namespace EasyCart\Controller;

use EasyCart\Services\CartService;
use EasyCart\Services\PricingService;
use EasyCart\Collection\Collection_Product;
use EasyCart\Collection\Collection_Category;
use EasyCart\View\View_Cart_Index;

/**
 * Controller_Cart
 * 
 * Handles cart page and AJAX operations.
 */
class Controller_Cart
{
    private $cartService;
    private $pricingService;

    public function __construct()
    {
        $this->cartService = new CartService();
        $this->pricingService = new PricingService();
    }

    /**
     * Cart page
     */
    public function index()
    {
        $cart = $this->cartService->get();
        $cart_items = [];

        $productCollection = new Collection_Product();

        foreach ($cart as $productId => $quantity) {
            $productCollection->reset();
            $productCollection->addFilter('p.entity_id', $productId)->setLimit(1)->load();
            $product = $productCollection->getFirstItem();

            if ($product) {
                $adjusted_quantity = min($quantity, $product['stock'], CartService::MAX_QUANTITY_PER_ITEM);
                $shipping_type = ($product['price'] >= 300) ? 'Freight Shipping' : 'Express Shipping';

                $cart_items[] = [
                    'product' => $product,
                    'quantity' => $adjusted_quantity,
                    'total' => $product['price'] * $adjusted_quantity,
                    'shipping_type' => $shipping_type
                ];
            }
        }

        $categories = (new Collection_Category())
            ->addActiveFilter()
            ->setPositionOrder()
            ->load()
            ->getItems();

        $pricing = $this->pricingService->calculateAll($cart, 'standard');

        $view = new View_Cart_Index();
        $view->setDataArray([
            'page_title' => 'Shopping Cart',
            'cart_items' => $cart_items,
            'categories' => $categories,
            'pricing' => $pricing
        ]);

        echo $view->toHtml();
    }

    /**
     * Add to cart (AJAX)
     */
    public function add()
    {
        header('Content-Type: application/json');

        $productId = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
        $quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;

        $result = $this->cartService->add($productId, $quantity);

        echo json_encode([
            'success' => $result['success'],
            'cart_count' => $this->cartService->getCount(),
            'message' => $result['success'] ? 'Product added to cart' : 'Failed to add product'
        ]);
    }

    /**
     * Update cart (AJAX)
     */
    public function update()
    {
        header('Content-Type: application/json');

        $productId = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
        $quantity = isset($_POST['quantity']) ? (int) $_POST['quantity'] : 1;

        $result = $this->cartService->update($productId, $quantity);

        echo json_encode([
            'success' => true,
            'cart_count' => $this->cartService->getCount(),
            'actual_quantity' => $result['actual_quantity']
        ]);
    }

    /**
     * Remove from cart (AJAX)
     */
    public function remove()
    {
        header('Content-Type: application/json');

        $productId = isset($_POST['product_id']) ? (int) $_POST['product_id'] : 0;
        $this->cartService->remove($productId);

        echo json_encode([
            'success' => true,
            'cart_count' => $this->cartService->getCount()
        ]);
    }
}
