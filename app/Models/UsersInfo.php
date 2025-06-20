<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersInfo extends Model
{
    protected $fillable = [
        'user_id', 'legal_name', 'legal_address', 'inn', 'kpp', 'ogrn', 'phone', 'email', 'adress_ur'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
