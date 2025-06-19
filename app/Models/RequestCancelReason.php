<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RequestCancelReason extends Model
{
    public int $id;
    public string $reason;
    public int $request_id;
    public int $seller_id;
    public int $customer_id;

    protected $fillable = [
        'reason', 'request_id', 'seller_id', 'customer_id'
    ];

    public function request()
    {
        return $this->belongsTo(CustomerRequest::class, 'request_id');
    }

    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function customer()
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}
