<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MerchandiseStockOut extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'stock_out_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(MerchandiseStockOutItem::class, 'stock_out_id');
    }

    public function stockOpname(): BelongsTo
    {
        return $this->belongsTo(MerchandiseStockOpname::class, 'stock_opname_id');
    }
}
