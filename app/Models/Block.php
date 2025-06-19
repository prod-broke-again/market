<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    public int $id;
    public string $blockable_type;
    public int $blockable_id;
    public ?string $reason;
    public ?int $blocked_by;
    public ?string $blocked_until;

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
