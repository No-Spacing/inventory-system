<?php

namespace App\Imports;

use App\Models\Stockcard;
use Maatwebsite\Excel\Concerns\ToModel;

class StockcardImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Stockcard([
            'product_id' => $row[0],
            'lot_id' => $row[1],
            'quantity' => $row[2],
            'enduser_id' => $row[3],
            'transaction_type' => 'inbound',
            'warehouse_id' => $row[4],
            'remarks' => $row[5],
            'transaction_date' => now(),
        ]);
    }
}
