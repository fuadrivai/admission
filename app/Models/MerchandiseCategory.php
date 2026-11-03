<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MerchandiseCategory extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
        'is_store_visible' => 'boolean',
        'display_order' => 'integer',
    ];

    public function products(): HasMany
    {
        return $this->hasMany(MerchandiseProduct::class, 'category_id');
    }
}
