<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EnrolmentDiscountRule extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'percentage' => 'float',
        'requires_dp' => 'boolean',
        'is_active' => 'boolean',
        'quota' => 'integer',
        'valid_from' => 'datetime:Y-m-d H:i',
        'valid_date' => 'datetime:Y-m-d H:i',
        'va_valid_date' => 'datetime:Y-m-d H:i',
        'va_valid_days' => 'integer',
    ];

    public function usedCount(): int
    {
        return EnrolmentTransaction::where('discount_rule_id', $this->id)
            ->whereNotIn('payment_status', ['EXPIRED', 'FAILED'])
            ->count();
    }
}
