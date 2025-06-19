<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attachmentable extends Model
{
    public int $id;
    public string $attachmentable_type;
    public int $attachmentable_id;
    public int $attachment_id;

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
