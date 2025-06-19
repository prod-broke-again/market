<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slider extends Model
{
    public int $id;
    public string $image;
    public string $url;
    public ?int $width;
    public ?int $height;

    protected $fillable = [
        'image', 'url', 'width', 'height'
    ];
}
