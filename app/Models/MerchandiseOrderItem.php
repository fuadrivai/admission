<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MerchandiseOrderItem extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'float',
        'subtotal' => 'float',
        'is_preorder' => 'boolean',
        'preorder_expected_at' => 'date',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(MerchandiseOrder::class, 'order_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(MerchandiseVariant::class, 'variant_id');
    }

    public function preorderAllocations(): HasMany
    {
        return $this->hasMany(MerchandisePreorderAllocation::class, 'order_item_id');
    }
}
