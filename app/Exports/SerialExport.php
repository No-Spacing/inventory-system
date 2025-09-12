<?php

namespace App\Exports;

use App\Models\Stockcard;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SerialExport implements FromCollection, WithHeadings
{
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
            'Code',
            'Description',
            'Serial No.',
            'Quantity',
            'Transaction Type',
            'Warehouse',
            'Transaction Date'
        ];
    }
}
