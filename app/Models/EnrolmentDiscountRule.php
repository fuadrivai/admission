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
        'valid_date' => 'date:Y-m-d',
    ];

    public function usedCount(): int
    {
        return EnrolmentTransaction::where('discount_rule_id', $this->id)
            ->whereNotIn('payment_status', ['EXPIRED', 'FAILED'])
            ->count();
    }
}
