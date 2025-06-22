<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

use App\Models\Unit;
use App\Models\Category;
use App\Models\Serial;
use App\Models\Lot;

class Product extends Model
{
    protected $fillable = [
        'code',
        'description',
        'category_id',
        'unit_id',
        'type',
    ];

    public function unit(): HasOne
    {
        return $this->hasOne(Unit::class, 'id', 'unit_id');
    }

    public function category(): HasOne
    {
        return $this->hasOne(Category::class, 'id', 'category_id');
    }
    
    public function lot(): HasOne
    {
        return $this->hasOne(Lot::class, 'product_id', 'id');
    }

    public function serial(): HasOne
    {
        return $this->hasOne(Serial::class, 'product_id', 'id');
    }

    public function lots(): HasMany
    {
        return $this->hasMany(Lot::class);
    }

    public function serials(): HasMany
    {
        return $this->hasMany(Serial::class);
    }

}
