<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'supplier_id',
        'customer_id',
        'no',
        'status',
        'batch',
        'product_batch',
        'signing_at',
        'remark',
        'finished_at',
    ];

    protected $casts = [
        'product_batch'     => 'array',
    ];

    public function orderProduct()
    {
        return $this->hasMany(OrderProduct::class);
    }

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function orderBatch()
    {
        return $this->hasMany(OrderBatch::class)->orderBy('id', 'DESC');
    }
}
