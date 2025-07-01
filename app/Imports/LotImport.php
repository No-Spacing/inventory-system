<?php

namespace App\Imports;

use App\Models\Lot;
use Maatwebsite\Excel\Concerns\ToModel;

class LotImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Lot([
            'lot' => $row[0],
            'expiration_date' => $row[1]
                                 ? \Carbon\Carbon::parse($row[1])->setTimezone('Asia/Manila')->toDateString()
                                 : null,
            'product_id' => $row[2],
        ]);
    }
}
