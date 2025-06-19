<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    public int $id;
    public ?string $product_name;
    public ?string $brand;
    public ?array $product_images;
    public ?array $product_colors;
    public ?int $seller_id;
    public ?string $product_description;
    public ?int $location_id;
    public ?int $category_id;
    public ?float $price;
    public ?float $price_discount;
    public ?int $status_placement;
    public ?int $availability;
    public ?int $is_draft;
    public ?string $articul;
    public ?int $count;

    protected $casts = [
        'product_images' => 'array',
        'product_colors' => 'array',
        'price' => 'float',
        'price_discount' => 'float',
        'status_placement' => 'integer',
        'availability' => 'integer',
        'is_draft' => 'integer',
        'count' => 'integer',
    ];

    protected $fillable = [
        'product_name', 'brand', 'product_images', 'product_colors', 'seller_id', 'product_description', 'location_id', 'category_id', 'price', 'price_discount', 'status_placement', 'availability', 'is_draft', 'articul', 'count'
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function location()
    {
        return $this->belongsTo(Address::class, 'location_id');
    }

    public function characteristics()
    {
        return $this->hasMany(ProductCharacteristic::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function attachments()
    {
        return $this->morphMany(Attachmentable::class, 'attachmentable');
    }
}
