<?php

namespace EasyCart\Model;

/**
 * Model_Category
 * 
 * Category entity with business logic.
 */
class Model_Category extends Model_Abstract
{
    public function getName(): string
    {
        return $this->getData('name') ?? '';
    }

    public function getUrlKey(): string
    {
        return $this->getData('url_key') ?? '';
    }

    public function isActive(): bool
    {
        return (bool) $this->getData('is_active');
    }

    public function getPosition(): int
    {
        return (int) ($this->getData('position') ?? 0);
    }
}
