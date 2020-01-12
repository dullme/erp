<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderBatch extends Model
{

    protected $fillable = [
        'status',
        'order_id',
        'entry_at',
        'batch',
        'product_batch'
    ];

    protected $casts = [
        'product_batch' => 'array',
    ];
}
