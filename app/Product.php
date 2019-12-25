<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class);
    }
}
