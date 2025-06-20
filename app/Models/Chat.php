<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Chat extends Model
{
    protected $fillable = [
        'response_id', 'seller_id', 'customer_id', 'status'
    ];

    public function response()
    {
        return $this->belongsTo(Response::class);
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }
}
