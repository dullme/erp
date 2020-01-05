<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{

    protected $fillable = [
        'forwarding_company_id',
        'lading_number',
        'container_number',
        'seal_number',
        'product',
        'packaged_at',
        'remark',
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
}
