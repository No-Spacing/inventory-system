<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

use App\Models\Product;
use App\Models\Lot;
use App\Models\Serial;

class Stockcard extends Model
{
    protected $fillable = [
        'product_id',
        'lot_id',
        'approver_id',
        'enduser_id',
        'serial_id',
        'transaction_type',
        'quantity',
        'item_balance',
        'transaction_date',
        'expiration_date',
        'document_no',
        'warehouse',
        'reference',
        'remarks',
    ];

    public function product(): HasOne
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

    public function lot(): HasOne
    {
        return $this->hasOne(Lot::class, 'id', 'lot_id');
    }

    public function serial(): HasOne
    {
        return $this->hasOne(Serial::class, 'id', 'serial_id');
    }

}
