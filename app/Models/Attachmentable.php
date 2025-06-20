<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachmentable extends Model
{
    protected $fillable = [
        'attachmentable_type', 'attachmentable_id', 'attachment_id'
    ];

    public function attachmentable()
    {
        return $this->morphTo();
    }

    public function attachment()
    {
        return $this->belongsTo(Attachment::class);
    }
}
