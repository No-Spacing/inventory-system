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
            'serial_id' => $row[2],
            'quantity' => $row[3],
            'enduser_id' => $row[4],
            'transaction_type' => 'inbound',
            'warehouse_id' => $row[5],
            'remarks' => $row[6],
            'transaction_date' => now(),
        ]);
    }
}
