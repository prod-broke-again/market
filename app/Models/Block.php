<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    protected $fillable = [
        'blockable_type', 'blockable_id', 'reason', 'blocked_by', 'blocked_until'
    ];

    public function blockable()
    {
        return $this->morphTo();
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'blocked_by');
    }
}
