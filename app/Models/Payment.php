<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    public int $id;
    public int $user_id;
    public int $order_id;
    public float $sum;
    public string $status;

    protected $fillable = [
        'user_id', 'order_id', 'sum', 'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
