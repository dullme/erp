<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Compose extends Model
{
    //

    public function composeProducts(){
        return $this->hasMany(ComposeProduct::class);
    }
}
