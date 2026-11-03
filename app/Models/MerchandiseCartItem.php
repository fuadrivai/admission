<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MerchandiseCartItem extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'quantity' => 'integer',
        'is_selected' => 'boolean',
    ];

    public function cart(): BelongsTo
    {
        return $this->belongsTo(MerchandiseCart::class, 'cart_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(MerchandiseVariant::class, 'variant_id');
    }
}
