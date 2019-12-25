<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'status',
        'quantity',
    ];
}
