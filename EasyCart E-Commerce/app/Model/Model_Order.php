<?php

namespace EasyCart\Model;

/**
 * Model_Order
 * 
 * Order entity with business logic.
 */
class Model_Order extends Model_Abstract
{
    public function getOrderNumber(): string
    {
        return $this->getData('order_number') ?? '';
    }

    public function getStatus(): string
    {
        return $this->getData('status') ?? 'pending';
    }

    public function getTotal(): float
    {
        return (float) ($this->getData('total') ?? 0);
    }

    public function getSubtotal(): float
    {
        return (float) ($this->getData('subtotal') ?? 0);
    }

    public function getShippingCost(): float
    {
        return (float) ($this->getData('shipping_cost') ?? 0);
    }

    public function getTax(): float
    {
        return (float) ($this->getData('tax') ?? 0);
    }

    public function isArchived(): bool
    {
        return (bool) $this->getData('is_archived');
    }

    public function canCancel(): bool
    {
        return in_array($this->getStatus(), ['pending', 'processing']);
    }
}
