<?php

namespace EasyCart\View;

/**
 * View_Order_Detail — Single Order Detail / Invoice Page
 */
class View_Order_Detail extends View_Abstract
{
    /** @var string|null If set, uses the invoice template */
    private $viewType = 'detail';

    public function __construct(array $data = [], string $viewType = 'detail')
    {
        parent::__construct($data);
        $this->viewType = $viewType;

        if ($viewType === 'invoice') {
            $this->template = __DIR__ . '/Templates/orders/invoice.php';
        } else {
            $this->template = __DIR__ . '/Templates/orders/detail.php';
        }
    }
}
