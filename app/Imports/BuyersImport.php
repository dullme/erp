<?php

namespace App\Imports;

use App\Buyer;
use Maatwebsite\Excel\Concerns\ToModel;

class BuyersImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return $row;
    }
}
