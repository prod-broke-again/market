<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerRequest extends Model
{
    public int $id;
    public int $user_id;
    public string $name;
    public string $description;
    public ?array $images;
    public ?float $min_price;
    public ?float $max_price;
    public ?int $category_id;
    public bool $is_draft;
    public string $status;
    public ?int $response_id;

    protected $casts = [
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
