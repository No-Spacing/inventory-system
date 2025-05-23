<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'code',
        'description',
        'category_id',
        'unit_id',
        'type',
    ];
}
