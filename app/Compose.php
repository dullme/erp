<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Compose extends Model
{
    protected $fillable = [
        'name',
        'asin',
        'image',
        'hq',
        'content'
    ];

    public function composeProducts(){
        return $this->hasMany(ComposeProduct::class)->with('product');
    }

    public function setImageAttribute($image)
    {
        if (is_array($image)) {
            $this->attributes['image'] = json_encode($image);
        }
    }

    public function getImageAttribute($image)
    {
        return json_decode($image, true);
    }
}
