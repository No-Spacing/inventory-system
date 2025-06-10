<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

use App\Models\Product;

class Stockcard extends Model
{
    protected $fillable = [
        'product_id',
        'lot_id',
        'approver_id',
        'enduser_id',
        'serial_no',
        'transaction_type',
        'quantity',
        'item_balance',
        'transaction_date',
        'expiration_date',
        'warehouse',
        'reference',
        'remarks',
    ];

    public function product(): HasOne
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

}
