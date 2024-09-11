<?php

namespace App\Models;

use App\PaymentProviderEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $casts = [];

    /**
     * Define the relationship between Payment and Order.
     * Each payment belongs to one order.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }
}
