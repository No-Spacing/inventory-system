<?php

namespace App\Imports;

use App\Models\Serial;
use Maatwebsite\Excel\Concerns\ToModel;

class SerialImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Serial([
            'serial_no' => $row[0],
            'product_id' => $row[1],
        ]);
    }
}
