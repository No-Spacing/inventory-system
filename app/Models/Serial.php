<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Models\Stockcard;

class Serial extends Model
{
    protected $fillable = [
        'serial_no',
        'product_id'
    ];

}
