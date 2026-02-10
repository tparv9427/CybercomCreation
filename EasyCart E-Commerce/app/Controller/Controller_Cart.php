<?php

namespace EasyCart\Controller;

use EasyCart\Services\CartService;
use EasyCart\Services\PricingService;
use EasyCart\Services\EmailCartService;
use EasyCart\Collection\Collection_Product;
use EasyCart\Collection\Collection_Category;
use EasyCart\View\View_Cart;
use EasyCart\Core\View;

/**
 * Controller_Cart — Cart Controller
 * 
 * Handles cart page display, add/update/remove (AJAX), save-for-later.
 * No SQL, no HTML.
 */
class Controller_Cart extends Controller_Abstract
{
    private $cartService;
    private $pricingService;
    private $productCollection;
    private $categoryCollection;
    private $emailCartService;

    public function __construct()
    {
        $this->cartService = new CartService();
        $this->pricingService = new PricingService();
        $this->productCollection = new Collection_Product();
        $this->categoryCollection = new Collection_Category();
        $this->emailCartService = new EmailCartService();
    }

    /**
     * Cart page
     */
    public function index(): void
    {
        $categories = $this->categoryCollection->getAll();

        $cart = $this->cartService->get();
        $cart_items = [];

        foreach ($cart as $product_id => $quantity) {
            $product = $this->productCollection->findById($product_id);
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

        $saved_items = $this->cartService->getSavedItems();
        $pricing = $this->pricingService->calculateAll($cart, 'standard');
        $estimated_totals = $this->pricingService->calculateEstimatedTotalRange($cart);
        $shipping_category = $this->pricingService->determineShippingCategory($cart);

        $contentView = new View_Cart([
            'cart_items' => $cart_items,
            'saved_items' => $saved_items,
            'pricing' => $pricing,
            'estimated_totals' => $estimated_totals,
            'shipping_category' => $shipping_category,
            'categories' => $categories,
            'formatPrice' => [\EasyCart\Helpers\ViewHelper::class, 'formatPrice'],
            'getCategory' => [\EasyCart\Helpers\ViewHelper::class, 'getCategory'],
        ]);

        $this->renderWithLayout($contentView, [
            'page_title' => 'Shopping Cart',
            'categories' => $categories,
        ]);
    }

    /**
     * Add to cart (AJAX)
     */
    public function add(): void
    {
        $product_id = (int) ($_POST['product_id'] ?? 0);
        $quantity = (int) ($_POST['quantity'] ?? 1);

        $result = $this->cartService->add($product_id, $quantity);

        if ($result['success']) {
            $cart = $this->cartService->get();
            $shipping_category = $this->pricingService->determineShippingCategory($cart);

            $message = 'Product added to cart';
            if ($result['limit_reached'] ?? false) {
                $message = "Only " . CartService::MAX_QUANTITY_PER_ITEM . " quantity allowed per cart.";
            } elseif ($result['max_stock_reached']) {
                $message = "Maximum stock reached! Only {$result['max_stock']} available.";
            }

            $this->jsonResponse([
                'success' => true,
                'cart_count' => $this->cartService->getCount(),
                'shipping_category' => $shipping_category,
                'message' => $message,
                'max_stock_reached' => $result['max_stock_reached'] || ($result['limit_reached'] ?? false),
                'current_quantity' => $result['current_quantity'],
                'max_stock' => $result['max_stock']
            ]);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Product not found']);
        }
    }

    /**
     * Update cart (AJAX)
     */
    public function update(): void
    {
        $product_id = (int) ($_POST['product_id'] ?? 0);
        $quantity = (int) ($_POST['quantity'] ?? 1);

        $result = $this->cartService->update($product_id, $quantity);
        $actual_quantity = $result['actual_quantity'];

        $cart = $this->cartService->get();
        $shipping_category = $this->pricingService->determineShippingCategory($cart);
        $pricing = $this->pricingService->calculateAll($cart, 'standard');

        $product = $this->productCollection->findById($product_id);
        $item_total = $product ? $product['price'] * $actual_quantity : 0;
        $max_stock = $result['max_stock'] ?? (min((int) ($product['stock'] ?? 0), CartService::MAX_QUANTITY_PER_ITEM));
        $estimated_totals = $this->pricingService->calculateEstimatedTotalRange($cart);

        $message = null;
        if ($quantity > $actual_quantity) {
            if ($result['limit_reached'] ?? false) {
                $message = "Only " . CartService::MAX_QUANTITY_PER_ITEM . " quantity allowed per cart.";
            } else {
                $message = "Maximum stock reached! Only {$actual_quantity} available.";
            }
        }

        $this->jsonResponse([
            'success' => true,
            'cart_count' => $this->cartService->getCount(),
            'actual_quantity' => $actual_quantity,
            'max_stock' => $max_stock,
            'message' => $message,
            'shipping_category' => $shipping_category,
            'item_total' => \EasyCart\Helpers\FormatHelper::price($item_total),
            'subtotal' => \EasyCart\Helpers\FormatHelper::price($pricing['subtotal']),
            'shipping' => \EasyCart\Helpers\FormatHelper::price($pricing['shipping']),
            'tax' => \EasyCart\Helpers\FormatHelper::price($pricing['tax']),
            'item_tax' => \EasyCart\Helpers\FormatHelper::price($estimated_totals['tax_on_items']),
            'total' => \EasyCart\Helpers\FormatHelper::price($pricing['total']),
            'cart_value' => \EasyCart\Helpers\FormatHelper::price($estimated_totals['cart_value']),
            'estimated_total_min' => \EasyCart\Helpers\FormatHelper::price($estimated_totals['min']),
            'estimated_total_max' => \EasyCart\Helpers\FormatHelper::price($estimated_totals['max'])
        ]);
    }

    /**
     * Remove from cart (AJAX)
     */
    public function remove(): void
    {
        $product_id = (int) ($_POST['product_id'] ?? 0);
        $this->cartService->remove($product_id);

        $cart = $this->cartService->get();
        $shipping_category = $this->pricingService->determineShippingCategory($cart);
        $pricing = $this->pricingService->calculateAll($cart, 'standard');
        $estimated_totals = $this->pricingService->calculateEstimatedTotalRange($cart);

        $this->jsonResponse([
            'success' => true,
            'cart_count' => $this->cartService->getCount(),
            'shipping_category' => $shipping_category,
            'message' => 'Product removed from cart',
            'subtotal' => \EasyCart\Helpers\FormatHelper::price($pricing['subtotal']),
            'shipping' => \EasyCart\Helpers\FormatHelper::price($pricing['shipping']),
            'tax' => \EasyCart\Helpers\FormatHelper::price($pricing['tax']),
            'item_tax' => \EasyCart\Helpers\FormatHelper::price($estimated_totals['tax_on_items']),
            'total' => \EasyCart\Helpers\FormatHelper::price($pricing['total']),
            'cart_value' => \EasyCart\Helpers\FormatHelper::price($estimated_totals['cart_value']),
            'estimated_total_min' => \EasyCart\Helpers\FormatHelper::price($estimated_totals['min']),
            'estimated_total_max' => \EasyCart\Helpers\FormatHelper::price($estimated_totals['max'])
        ]);
    }

    /**
     * Save for later (AJAX)
     */
    public function saveForLater(): void
    {
        $product_id = (int) ($_POST['product_id'] ?? 0);
        $this->cartService->saveForLater($product_id);

        $cart = $this->cartService->get();
        $pricing = $this->pricingService->calculateAll($cart, 'standard');
        $estimated_totals = $this->pricingService->calculateEstimatedTotalRange($cart);

        $product = $this->productCollection->findById($product_id);
        $savedItemHtml = '';
        if ($product) {
            $savedItemHtml = \EasyCart\Core\View::render('components/saved_item', [
                'item' => [
                    'product' => $product,
                    'quantity' => 1,
                    'formatted_price' => \EasyCart\Helpers\FormatHelper::price($product['price'])
                ]
            ]);
        }

        $this->jsonResponse([
            'success' => true,
            'cart_count' => $this->cartService->getCount(),
            'message' => 'Item saved for later',
            'subtotal' => \EasyCart\Helpers\FormatHelper::price($pricing['subtotal']),
            'shipping' => \EasyCart\Helpers\FormatHelper::price($pricing['shipping']),
            'tax' => \EasyCart\Helpers\FormatHelper::price($pricing['tax']),
            'item_tax' => \EasyCart\Helpers\FormatHelper::price($estimated_totals['tax_on_items']),
            'total' => \EasyCart\Helpers\FormatHelper::price($pricing['total']),
            'cart_value' => \EasyCart\Helpers\FormatHelper::price($estimated_totals['cart_value']),
            'estimated_total_min' => \EasyCart\Helpers\FormatHelper::price($estimated_totals['min']),
            'estimated_total_max' => \EasyCart\Helpers\FormatHelper::price($estimated_totals['max']),
            'saved_item_html' => $savedItemHtml
        ]);
    }

    /**
     * Move to cart from saved (AJAX)
     */
    public function moveToCart(): void
    {
        $product_id = (int) ($_POST['product_id'] ?? 0);
        $this->cartService->moveToCartFromSaved($product_id);

        $cart = $this->cartService->get();
        $pricing = $this->pricingService->calculateAll($cart, 'standard');
        $estimated_totals = $this->pricingService->calculateEstimatedTotalRange($cart);

        $product = $this->productCollection->findById($product_id);
        $cartItemHtml = '';
        if ($product) {
            $quantity = $cart[$product_id] ?? 1;
            $category = $this->categoryCollection->findById($product['category_id'] ?? 0);
            $cartItemHtml = \EasyCart\Core\View::render('components/cart_item', [
                'item' => [
                    'product' => $product,
                    'quantity' => $quantity,
                    'total' => $product['price'] * $quantity,
                    'category_name' => $category ? $category['name'] : 'Others',
                    'formatted_price' => \EasyCart\Helpers\FormatHelper::price($product['price']),
                    'formatted_total' => \EasyCart\Helpers\FormatHelper::price($product['price'] * $quantity),
                    'shipping_type' => ($product['price'] >= 300) ? 'Freight Shipping' : 'Express Shipping'
                ]
            ]);
        }

        $this->jsonResponse([
            'success' => true,
            'cart_count' => $this->cartService->getCount(),
            'message' => 'Item moved back to cart',
            'subtotal' => \EasyCart\Helpers\FormatHelper::price($pricing['subtotal']),
            'shipping' => \EasyCart\Helpers\FormatHelper::price($pricing['shipping']),
            'tax' => \EasyCart\Helpers\FormatHelper::price($pricing['tax']),
            'item_tax' => \EasyCart\Helpers\FormatHelper::price($estimated_totals['tax_on_items']),
            'total' => \EasyCart\Helpers\FormatHelper::price($pricing['total']),
            'cart_value' => \EasyCart\Helpers\FormatHelper::price($estimated_totals['cart_value']),
            'estimated_total_min' => \EasyCart\Helpers\FormatHelper::price($estimated_totals['min']),
            'estimated_total_max' => \EasyCart\Helpers\FormatHelper::price($estimated_totals['max']),
            'cart_item_html' => $cartItemHtml
        ]);
    }

    /**
     * Get cart count (AJAX)
     */
    public function count(): void
    {
        $this->jsonResponse(['success' => true, 'cart_count' => $this->cartService->getCount()]);
    }

    /**
     * Check email for add-to-cart flow (AJAX)
     */
    public function checkEmail(): void
    {
        $email = $_POST['email'] ?? '';
        $result = $this->emailCartService->checkEmail($email);
        $this->jsonResponse($result);
    }
}
