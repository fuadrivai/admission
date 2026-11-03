<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MerchandiseStockOpnameItem extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'system_quantity' => 'integer',
        'counted_quantity' => 'integer',
        'difference' => 'integer',
    ];

    public function opname(): BelongsTo
    {
        return $this->belongsTo(MerchandiseStockOpname::class, 'opname_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(MerchandiseVariant::class, 'variant_id');
    }
}
