<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public int $id;
    public int $user_id;
    public float $sum;
    public string $status;

    protected $fillable = [
        'user_id', 'sum', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
