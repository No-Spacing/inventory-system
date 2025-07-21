<?php

namespace App\Exports;

use App\Models\Stockcard;
use Maatwebsite\Excel\Concerns\FromCollection;

class StockcardExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // return Stockcard::all();
        return Stockcard::with(['product', 'warehouse', 'lot', 'serial'])->get()->map(function ($stockcard) {
            return [
                'Product Code' => $stockcard->product->code,
                'Lot/Serial' => optional($stockcard->lot)->lot . optional($stockcard->serial)->serial_no,
                'Quantity' => $stockcard->quantity,
                'Transaction Type' => $stockcard->transaction_type,
                'Date' => $stockcard->created_at->format('Y-m-d'),
                'Warehouse' => $stockcard->warehouse->warehouse
            ];
        });
    }
}
