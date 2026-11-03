<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MerchandiseStockOutItem extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'quantity' => 'integer',
        'unit_cost' => 'float',
    ];

    public function stockOut(): BelongsTo
    {
        return $this->belongsTo(MerchandiseStockOut::class, 'stock_out_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(MerchandiseVariant::class, 'variant_id');
    }
}
