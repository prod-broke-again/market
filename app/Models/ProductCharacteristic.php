<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCharacteristic extends Model
{
    public int $id;
    public int $product_id;
    public string $characteristic_name;
    public string $characteristic_value;

    protected $fillable = [
        'product_id', 'characteristic_name', 'characteristic_value'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
