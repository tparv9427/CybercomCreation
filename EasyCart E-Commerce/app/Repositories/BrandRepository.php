<?php

namespace EasyCart\Repositories;

/**
 * BrandRepository
 * 
 * Migrated from: includes/config.php (lines 36-49, 168-171)
 */
class BrandRepository
{
    private $brands;

    public function __construct()
    {
        global $brands;
        $this->brands = $brands ?? $this->getDefaultBrands();
    }

    private function getDefaultBrands()
    {
        return [
            1 => ['id' => 1, 'name' => 'TechPro'],
            2 => ['id' => 2, 'name' => 'StyleMax'],
            3 => ['id' => 3, 'name' => 'HomeComfort'],
            4 => ['id' => 4, 'name' => 'SportFit'],
            5 => ['id' => 5, 'name' => 'ReadMore'],
            6 => ['id' => 6, 'name' => 'ElectroPlus'],
            7 => ['id' => 7, 'name' => 'FashionHub'],
            8 => ['id' => 8, 'name' => 'GadgetWorld'],
            9 => ['id' => 9, 'name' => 'UrbanStyle'],
            10 => ['id' => 10, 'name' => 'CozyHome'],
            11 => ['id' => 11, 'name' => 'ActiveLife'],
            12 => ['id' => 12, 'name' => 'BookNest']
        ];
    }

    public function getAll()
    {
        return $this->brands;
    }

    public function find($id)
    {
        return isset($this->brands[$id]) ? $this->brands[$id] : null;
    }
}
