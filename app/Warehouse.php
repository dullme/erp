<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = [
        'order_id',
        'package_id',
        'warehouse_company_id',
        'order_batch',
        'batch_number',
        'product_id',
        'status',
        'quantity',
        'entry_at',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function warehouseCompany()
    {
        return $this->belongsTo(WarehouseCompany::class);
    }
}
