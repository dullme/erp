<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{

    protected $fillable = [
        'status',
        'forwarding_company_id',
        'warehouse_company_id',
        'buyer_id',
        'customer_id',
        'lading_number',
        'agreement_no',
        'container_number',
        'seal_number',
        'product',
        'ship_port',
        'arrival_port',
        'remark',
        'report',
        'packaged_at',
        'departure_at',
        'arrival_at',
        'entry_at',
        'checkin_at',
    ];

    protected $casts = [
        'product' => 'array',
    ];

    public function warehouse()
    {
        return $this->hasMany(Warehouse::class);
    }

    public function forwardingCompany()
    {
        return $this->belongsTo(ForwardingCompany::class);
    }

    public function buyer()
    {
        return $this->belongsTo(Buyer::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function warehouseCompany()
    {
        return $this->belongsTo(WarehouseCompany::class);
    }
}
