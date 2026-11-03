<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MerchandiseProduct extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'is_active' => 'boolean',
        'is_published' => 'boolean',
        'display_order' => 'integer',
        'sale_start_at' => 'datetime',
        'sale_end_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(MerchandiseCategory::class, 'category_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(MerchandiseVariant::class, 'product_id');
    }

    public function images(): HasMany
    {
        return $this->hasMany(MerchandiseProductImage::class, 'product_id')->orderBy('display_order');
    }
}
