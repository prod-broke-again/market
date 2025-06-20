<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UsersInfo extends Model
{
    protected $fillable = [
        'user_id',
        'avatar',
        'address_id',
        'legal_name',
        'inn',
        'kpp',
        'ogrn',
        'phone',
        'email',
        'adress_ur'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }
}
