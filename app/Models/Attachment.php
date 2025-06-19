<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
    public int $id;
    public string $name;
    public string $original_name;
    public string $mime;
    public ?string $extension;
    public int $size;
    public int $sort;
    public string $path;
    public ?string $description;
    public ?string $alt;
    public ?string $hash;
    public string $disk;
    public ?int $user_id;
    public ?string $group;

    protected $fillable = [
        'name', 'original_name', 'mime', 'extension', 'size', 'sort', 'path', 'description', 'alt', 'hash', 'disk', 'user_id', 'group'
    ];

    public function attachmentables()
    {
        return $this->morphToMany(Attachmentable::class, 'attachmentable');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
