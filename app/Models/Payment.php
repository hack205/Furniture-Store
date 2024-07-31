<?php

namespace App\Models;

use App\PaymentProviderEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $casts = [
        'method' =>  PaymentProviderEnum::class,
    ];
}
