<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ComposeProduct extends Model
{
    protected $fillable = [
        'compose_id',
        'product_id',
        'quantity',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function compose()
    {
        return $this->belongsTo(Compose::class);
    }
}
