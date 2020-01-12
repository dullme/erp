<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'inspection_at',
        'remark',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
