<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

use App\Models\Stockcard;
use App\Models\Product;

class Lot extends Model
{
    protected $fillable = [
        'lot',
        'expiration_date',
        'product_id',
    ];

    public function product(): HasOne
    {
        return $this->hasOne(Product::class, 'id', 'product_id');
    }

}
