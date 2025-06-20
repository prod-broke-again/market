<?php

namespace App\Models;

use App\Enums\ResponseStatus;
use Illuminate\Database\Eloquent\Model;

class Response extends Model
{
    protected $casts = [
        'price' => 'float',
        'status' => ResponseStatus::class,
    ];

    protected $fillable = [
        'seller_id', 'request_id', 'description', 'price', 'status'
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

    public function messages()
    {
        return $this->hasManyThrough(ChatMessage::class, Chat::class);
    }
}
