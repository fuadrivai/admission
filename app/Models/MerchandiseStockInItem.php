<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
