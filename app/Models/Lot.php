<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lot extends Model
{
    protected $fillable = [
        'lot',
        'total_items',
        'expiration_date',
    ];
}
