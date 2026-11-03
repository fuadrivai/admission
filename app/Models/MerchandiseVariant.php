<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MerchandiseVariant extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'price' => 'float',
        'low_stock_threshold' => 'integer',
        'online_stock_limit' => 'integer',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(MerchandiseProduct::class, 'product_id');
    }

    public function stockInItems(): HasMany
    {
        return $this->hasMany(MerchandiseStockInItem::class, 'variant_id');
    }

    public function sellingPriceHistories(): HasMany
    {
        return $this->hasMany(MerchandiseSellingPriceHistory::class, 'variant_id');
    }
}
