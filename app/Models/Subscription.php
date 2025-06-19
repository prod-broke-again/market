<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    public int $id;
    public int $user_id;
    public int $level;
    public ?string $expired_at;
    public bool $is_active;

    protected $casts = [
        'level' => 'integer',
        'is_active' => 'boolean',
        'expired_at' => 'datetime',
    ];

    protected $fillable = [
        'user_id', 'level', 'expired_at', 'is_active'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
