<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MerchandiseOrder extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'total_items' => 'integer',
        'subtotal' => 'float',
        'bank_charger' => 'float',
        'discount_amount' => 'float',
        'total_amount' => 'float',
        'expires_at' => 'datetime',
        'paid_at' => 'datetime',
        'invoice_email_sent_at' => 'datetime',
        'receipt_email_sent_at' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(MerchandiseOrderItem::class, 'order_id');
    }

    public function promotion(): BelongsTo
    {
        return $this->belongsTo(MerchandisePromotion::class, 'promotion_id');
    }
}
