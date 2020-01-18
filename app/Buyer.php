<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Buyer extends Model
{
    protected $fillable = [
        'name',
        'english_name',
        'contact_person',
        'position',
        'mobile',
        'tel',
        'fax',
        'email',
        'website',
        'address',
        'supply',
        'tax_id',
        'bank',
        'bank_account',
        'register',
        'remark',
    ];
}
