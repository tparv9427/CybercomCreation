<?php

namespace EasyCart\Models;

class Category
{
    public $id;
    public $name;
    public $slug;

    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug
        ];
    }

    public static function fromArray($data)
    {
        $category = new self();
        foreach ($data as $key => $value) {
            if (property_exists($category, $key)) {
                $category->$key = $value;
            }
        }
        return $category;
    }
}
