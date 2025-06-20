<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;

    public const bool DEFAULT_AVAILABILITY = true;

    protected $casts = [
        'product_images' => 'array',
        'product_colors' => 'array',
        'price' => 'float',
        'price_discount' => 'float',
        'availability' => 'boolean',
        'is_draft' => 'boolean',
        'count' => 'integer',
    ];

    protected $fillable = [
        'product_name',
        'slug',
        'product_description',
        'product_images',
        'product_colors',
        'seller_id',
        'category_id',
        'price',
        'price_discount',
        'availability',
        'is_draft',
        'articul',
        'count',
        'brand',
        'location_id',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'location_id');
    }

    public function characteristics()
    {
        return $this->hasMany(ProductCharacteristic::class);
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    public function attachments()
    {
        return $this->morphMany(Attachmentable::class, 'attachmentable');
    }
}
