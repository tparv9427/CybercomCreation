<?php

namespace EasyCart\Models;

/**
 * Product Model
 * 
 * Represents a product entity in the e-commerce system.
 */
class Product
{
    public $id;
    public $name;
    public $slug;
    public $description;
    public $long_description;
    public $price;
    public $original_price;
    public $discount_percent;
    public $category_id;
    public $brand_id;
    public $rating;
    public $reviews_count;
    public $stock;
    public $featured;
    public $new;
    public $icon;
    public $features;
    public $specifications;
    public $variants;

    /**
     * Convert model to array
     * 
     * @return array
     */
    public function toArray()
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'long_description' => $this->long_description,
            'price' => $this->price,
            'original_price' => $this->original_price,
            'discount_percent' => $this->discount_percent,
            'category_id' => $this->category_id,
            'brand_id' => $this->brand_id,
            'rating' => $this->rating,
            'reviews_count' => $this->reviews_count,
            'stock' => $this->stock,
            'featured' => $this->featured,
            'new' => $this->new,
            'icon' => $this->icon,
            'features' => $this->features,
            'specifications' => $this->specifications,
            'variants' => $this->variants
        ];
    }

    /**
     * Create model from array
     * 
     * @param array $data
     * @return Product
     */
    public static function fromArray($data)
    {
        $product = new self();
        foreach ($data as $key => $value) {
            if (property_exists($product, $key)) {
                $product->$key = $value;
            }
        }
        return $product;
    }
}
