<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachment extends Model
{
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
