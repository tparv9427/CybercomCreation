<?php

namespace EasyCart\Repositories;

/**
 * CategoryRepository
 * 
 * Migrated from: includes/config.php (lines 27-33, 163-166)
 */
class CategoryRepository
{
    private $categories;

    public function __construct()
    {
        // Load from global config
        global $categories;
        $this->categories = $categories ?? $this->getDefaultCategories();
    }

    private function getDefaultCategories()
    {
        return [
            1 => ['id' => 1, 'name' => 'Electronics', 'slug' => 'electronics'],
            2 => ['id' => 2, 'name' => 'Fashion', 'slug' => 'fashion'],
            3 => ['id' => 3, 'name' => 'Home & Living', 'slug' => 'home-living'],
            4 => ['id' => 4, 'name' => 'Sports', 'slug' => 'sports'],
            5 => ['id' => 5, 'name' => 'Books', 'slug' => 'books']
        ];
    }

    public function getAll()
    {
        return $this->categories;
    }

    public function find($id)
    {
        return isset($this->categories[$id]) ? $this->categories[$id] : null;
    }
}
