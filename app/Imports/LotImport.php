<?php

namespace App\Imports;

use App\Models\Lot; 
use Maatwebsite\Excel\Concerns\ToModel;
use Carbon\Carbon;


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
                                ? Carbon::createFromFormat('m.d.y', $row[1])->format('Y-m-d')
                                : null,
            'product_id' => $row[2],
        ]);
    }
}
