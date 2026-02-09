<?php

namespace EasyCart\Collection;

/**
 * Collection_Category
 * 
 * Handles category queries with ordering.
 */
class Collection_Category extends Collection_Abstract
{
    protected $resourceClass = \EasyCart\Resource\Resource_Category::class;
    protected $modelClass = \EasyCart\Model\Model_Category::class;

    protected function initSelect(): void
    {
        $this->queryBuilder
            ->from('catalog_category_entity')
            ->select('*');
    }

    public function addActiveFilter(): self
    {
        $this->queryBuilder->where('is_active', true);
        return $this;
    }

    public function setPositionOrder(): self
    {
        $this->queryBuilder->orderBy('position', 'ASC');
        return $this;
    }
}
