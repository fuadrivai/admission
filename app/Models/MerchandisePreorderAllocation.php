<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MerchandisePreorderAllocation extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function orderItem(): BelongsTo
    {
        return $this->belongsTo(MerchandiseOrderItem::class, 'order_item_id');
    }

    public function stockInItem(): BelongsTo
    {
        return $this->belongsTo(MerchandiseStockInItem::class, 'stock_in_item_id');
    }
}
