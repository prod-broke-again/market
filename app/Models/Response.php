<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    public int $id;
    public int $seller_id;
    public int $request_id;
    public ?int $chat_id;
    public string $description;
    public float $price;
    public ?string $status;

    protected $casts = [
        'price' => 'float',
    ];

    protected $fillable = [
        'seller_id', 'request_id', 'chat_id', 'description', 'price', 'status'
    ];

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function customerRequest()
    {
        return $this->belongsTo(CustomerRequest::class, 'request_id');
    }

    public function chat()
    {
        return $this->hasOne(Chat::class);
    }
}
