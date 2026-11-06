<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MerchandiseStockInCostCorrection extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'old_unit_cost' => 'float',
        'new_unit_cost' => 'float',
    ];

    public function stockInItem(): BelongsTo
    {
        return $this->belongsTo(MerchandiseStockInItem::class, 'stock_in_item_id');
    }

    public function corrector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'corrected_by');
    }
}
