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
}
