<?php

namespace App\Models;

use App\Enums\CustomerRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerRequest extends Model
{
    use HasFactory;

    protected $casts = [
        'status' => CustomerRequestStatus::class,
        'images' => 'array',
        'min_price' => 'float',
        'max_price' => 'float',
        'is_draft' => 'boolean',
    ];

    protected $fillable = [
        'user_id', 'name', 'description', 'images', 'min_price', 'max_price', 'category_id', 'is_draft', 'status', 'response_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function responses()
    {
        return $this->hasMany(Response::class, 'request_id');
    }

    public function chats()
    {
        return $this->hasMany(Chat::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
