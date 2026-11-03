<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MerchandiseStoreSetting extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_open' => 'boolean',
    ];
}
