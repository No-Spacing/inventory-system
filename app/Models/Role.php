<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name'
    ];

    public const IS_ADMIN = 1;
    public const IS_SUPERVISOR = 2;
    public const IS_ENCODER = 3;

}
