<?php

namespace App\Exports;

use App\Models\Stockcard;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class WarehouseExport implements FromCollection, WithHeadings
{
    /**
    * @return \Illuminate\Support\Collection
    */
    protected $data;

    public function __construct($data)
    {
        $this->data = collect($data); // Ensure it's a collection
    }

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
        return [
            'code',
            'description',
            'unit',
            'transaction_date',
            'lotNo',
            'serialNo',
            'expiration_date',
            'transaction_type',
            'warehouse',
            'quantity',
            'reference',
            'remarks',
            'document_no',
            'created_at',
        ];
    }
}
