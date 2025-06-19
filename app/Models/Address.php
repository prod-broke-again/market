<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    public int $id;
    public ?string $address;
    public ?string $latitude;
    public ?string $longitude;
    public ?int $user_id;
    public ?int $product_id;
    public ?int $shop_id;

    protected $fillable = [
        'address', 'latitude', 'longitude', 'user_id', 'product_id', 'shop_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }
}
