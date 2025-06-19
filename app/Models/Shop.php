<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    public int $id;
    public int $user_id;
    public ?string $name;
    public ?string $description;
    public ?int $address_id;
    public ?string $work_times_start;
    public ?string $work_times_end;
    public ?string $phone;

    protected $fillable = [
        'user_id', 'name', 'description', 'address_id', 'work_times_start', 'work_times_end', 'phone'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function address()
    {
        return $this->belongsTo(Address::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
