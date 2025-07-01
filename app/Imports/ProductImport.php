<?php

namespace App\Imports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\ToModel;

class ProductImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Product([
            'code' => $row[0],
            'description' => $row[1],
            'unit_id' => $row[2],
            'category_id' => $row[3],
            'type' => $row[4],
        ]);
    }
}
