<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductCharacteristic extends Model
{
    protected $fillable = [
        'product_id', 'characteristic_name', 'characteristic_value'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
