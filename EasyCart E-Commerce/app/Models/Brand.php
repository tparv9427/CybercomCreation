<?php

namespace EasyCart\Models;

class Brand
{
    public $id;
    public $name;

    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name
        ];
    }

    public static function fromArray($data)
    {
        $brand = new self();
        foreach ($data as $key => $value) {
            if (property_exists($brand, $key)) {
                $brand->$key = $value;
            }
        }
        return $brand;
    }
}
