<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MerchandiseOrderItem extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'float',
        'subtotal' => 'float',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(MerchandiseOrder::class, 'order_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(MerchandiseVariant::class, 'variant_id');
    }
}
