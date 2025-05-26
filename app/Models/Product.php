<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Models\Unit;

class Product extends Model
{
    protected $fillable = [
        'code',
        'description',
        'category_id',
        'unit_id',
        'type',
        'remaining_items'
    ];

    public function unit(): HasOne
    {
        return $this->hasOne(Unit::class, 'id', 'unit_id');
    }
}
