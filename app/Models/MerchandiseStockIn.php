<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MerchandiseStockIn extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'stock_in_date' => 'date',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(MerchandiseStockInItem::class, 'stock_in_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function stockOpname(): BelongsTo
    {
        return $this->belongsTo(MerchandiseStockOpname::class, 'stock_opname_id');
    }
}
