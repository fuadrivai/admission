<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MerchandiseSellingPriceHistory extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'old_price' => 'float',
        'new_price' => 'float',
        'average_unit_cost' => 'float',
    ];

    public function variant(): BelongsTo
    {
        return $this->belongsTo(MerchandiseVariant::class, 'variant_id');
    }

    public function changer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
