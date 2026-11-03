<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MerchandisePromotionGiftOption extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'max_quantity' => 'integer',
    ];

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(MerchandisePromotion::class, 'promotion_id');
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(MerchandiseVariant::class, 'variant_id');
    }
}
