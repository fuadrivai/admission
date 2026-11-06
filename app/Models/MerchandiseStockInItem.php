<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MerchandiseStockInItem extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'quantity' => 'integer',
        'unit_cost' => 'float',
    ];

    public function stockIn(): BelongsTo
    {
        return $this->belongsTo(MerchandiseStockIn::class, 'stock_in_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(MerchandiseVariant::class, 'variant_id');
    }

    public function costCorrections(): HasMany
    {
        return $this->hasMany(MerchandiseStockInCostCorrection::class, 'stock_in_item_id');
    }

    public function preorderAllocations(): HasMany
    {
        return $this->hasMany(MerchandisePreorderAllocation::class, 'stock_in_item_id');
    }
}
