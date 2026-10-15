<?php

namespace App\Services;

use App\Models\EnrolmentDiscountRule;

class EnrolmentDiscountService
{
    /**
     * @param array $lineItems each with type and amount
     * @return array|null ['rule' => EnrolmentDiscountRule, 'index' => int, 'amount' => float]
     */
    public function resolve(?string $placeCode, array $lineItems, bool $lock = false, $branchId = null): ?array
    {
        if (!$placeCode) {
            return null;
        }

        $query = EnrolmentDiscountRule::where('is_active', true)
            ->where('registration_place_code', $placeCode)
            ->where(function ($q) {
                $q->where(function ($q) {
                    $q->whereNull('valid_from')->whereNull('valid_date');
                })->orWhere(function ($q) {
                    $q->where('valid_from', '<=', now())
                        ->where('valid_date', '>=', now());
                });
            })
            ->where(function ($q) use ($branchId) {
                $q->whereNull('branch_id');
                if ($branchId) {
                    $q->orWhere('branch_id', $branchId);
                }
            })
            ->orderByDesc('percentage');
        if ($lock) {
            $query->lockForUpdate();
        }

        $types = array_column($lineItems, 'type');
        foreach ($query->get() as $rule) {
            if ($rule->requires_dp && !in_array('dp', $types, true)) {
                continue;
            }
            $index = array_search($rule->applies_to_type, $types, true);
            if ($index === false || (float) $lineItems[$index]['amount'] <= 0) {
                continue;
            }
            if ($rule->quota !== null && $rule->usedCount() >= $rule->quota) {
                continue;
            }

            return [
                'rule' => $rule,
                'index' => $index,
                'amount' => round((float) $lineItems[$index]['amount'] * min($rule->percentage, 100) / 100, 2),
            ];
        }

        return null;
    }
}
