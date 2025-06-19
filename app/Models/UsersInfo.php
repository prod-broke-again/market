<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UsersInfo extends Model
{
    public int $id;
    public int $user_id;
    public ?string $legal_name;
    public ?string $legal_address;
    public ?string $inn;
    public ?string $kpp;
    public ?string $ogrn;
    public ?string $phone;
    public ?string $email;
    public ?string $adress_ur;

    protected $fillable = [
        'user_id', 'legal_name', 'legal_address', 'inn', 'kpp', 'ogrn', 'phone', 'email', 'adress_ur'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
