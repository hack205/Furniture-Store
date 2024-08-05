<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $casts = [
    ];

    protected $dates = [
        'archived_at'
    ];


    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function amountDue()
    {
        $totalPayments = $this->payments()->sum('amount');
        return $this->total - $totalPayments;
    }

    public function isSettled()
    {
        $totalPayments = $this->payments()->sum('amount');
        return $totalPayments >= $this->total;
    }
}
