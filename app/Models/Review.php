<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    public int $id;
    public int $user_id;
    public int $seller_id;
    public int $product_id;
    public int $rating;
    public string $comment;

    protected $fillable = [
        'user_id', 'seller_id', 'product_id', 'rating', 'comment'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
