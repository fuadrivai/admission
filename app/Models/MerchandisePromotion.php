<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MerchandisePromotion extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'discount_value' => 'float',
        'minimum_purchase' => 'float',
        'bundle_price' => 'float',
        'min_quantity' => 'integer',
        'buy_quantity' => 'integer',
        'get_quantity' => 'integer',
        'usage_limit' => 'integer',
        'is_active' => 'boolean',
        'valid_from' => 'datetime',
        'valid_until' => 'datetime',
    ];

    public function applicableVariants(): BelongsToMany
    {
        return $this->belongsToMany(
            MerchandiseVariant::class,
            'merchandise_promotion_variants',
            'promotion_id',
            'variant_id'
        );
    }

    public function bundleItems(): HasMany
    {
        return $this->hasMany(MerchandisePromotionBundleItem::class, 'promotion_id');
    }

    public function giftOptions(): HasMany
    {
        return $this->hasMany(MerchandisePromotionGiftOption::class, 'promotion_id');
    }

    public function giftVariant(): BelongsTo
    {
        return $this->belongsTo(MerchandiseVariant::class, 'gift_variant_id');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(MerchandiseOrder::class, 'promotion_id');
    }
}
