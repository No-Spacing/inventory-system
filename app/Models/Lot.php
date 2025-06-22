<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Stockcard;
use App\Models\Product;

class Lot extends Model
{
    protected $fillable = [
        'lot',
        'expiration_date',
        'product_id',
    ];

}
