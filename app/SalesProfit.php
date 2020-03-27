<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SalesProfit extends Model
{
    protected $fillable = [
        'id',
        'asin',
        'quantity',
        'ddp',
        'products',
    ];

    protected $casts = [
        'products'     => 'array',
    ];
}
