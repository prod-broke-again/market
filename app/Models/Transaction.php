<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    public int $id;
    public int $user_id;
    public ?int $product_id;
    public ?float $sum;
    public ?string $state;

    protected $fillable = [
        'user_id', 'product_id', 'sum', 'state'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
