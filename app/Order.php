<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'supplier_id',
        'no',
        'batch',
        'product_batch',
        'product',
        'signing_at',
        'remark',
    ];

    protected $casts = [
        'product'     => 'array',
        'product_batch'     => 'array',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
