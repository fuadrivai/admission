<?php

namespace App\Services\Implement;

use App\Models\EnrolmentPrice;
use App\Models\EnrolmentPriceItem;
use App\Services\EnrolmentPriceService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class EnrolmentPriceImplement implements EnrolmentPriceService
{
    public function get($with = [], $branchId = null)
    {
        return EnrolmentPrice::with(array_merge(['items'], $with))
            ->when($branchId, function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->get();
    }

    public function show($id, $with=[])
    {
        return EnrolmentPrice::with(array_merge(['items'], $with))->findOrFail($id);
    }

    public function post($data)
    {
        $enrolmentPrice = DB::transaction(function () use ($data) {
            $enrolmentPrice = EnrolmentPrice::create($this->headerData($data));
            $this->syncItems($enrolmentPrice, $data['items']);

            return $enrolmentPrice->load(['branch', 'level', 'items']);
        });

        return $enrolmentPrice;
    }

    public function put($data)
    {
        return DB::transaction(function () use ($data) {
            $enrolmentPrice = EnrolmentPrice::findOrFail($data['id']);
            $enrolmentPrice->update($this->headerData($data));
            $this->syncItems($enrolmentPrice, $data['items']);

            return $enrolmentPrice->load(['branch', 'level', 'items']);
        });
    }

    public function delete($id)
    {
        $enrolmentPrice = EnrolmentPrice::findOrFail($id);
        return $enrolmentPrice->delete();
    }

    public function getRegistrationPrice($branchId, $levelId, $academicYearId = null, $gradeId = null)
    {
        $price = $this->applicablePrice($academicYearId, $branchId, $levelId, $gradeId, true);
        if (!$price) {
            return null;
        }

        $priceItems = $this->priceItemsOrLegacyFallback($price);
        $price->setAttribute('price', $priceItems->sum(function ($item) {
            return (float) $item->amount;
        }));
        $price->setRelation('items', $priceItems);

        return $price;
    }

    public function getRequiredPriceItems($academicYearId, $branchId, $levelId, $gradeId): Collection
    {
        $price = $this->applicablePrice($academicYearId, $branchId, $levelId, $gradeId, true);
        return $price ? $this->priceItemsOrLegacyFallback($price) : collect();
    }

    private function applicablePrice($academicYearId, $branchId, $levelId, $gradeId, bool $withItems = false)
    {
        $query = EnrolmentPrice::query()
            ->where('branch_id', $branchId)
            ->where('level_id', $levelId)
            ->where('type', 'form')
            ->where('is_active', true)
            ->where(function ($query) use ($academicYearId) {
                $query->whereNull('academic_year_id');
                if ($academicYearId) {
                    $query->orWhere('academic_year_id', $academicYearId);
                }
            })
            ->where(function ($query) use ($gradeId) {
                $query->whereNull('grade_id');
                if ($gradeId) {
                    $query->orWhere('grade_id', $gradeId);
                }
            })
            ->orderByDesc('id');

        if ($withItems) {
            $query->with(['items' => function ($items) {
                $items->orderBy('id');
            }]);
        }

        return $query->get()
            ->sortByDesc(function ($candidate) use ($academicYearId, $gradeId) {
                return (int) ((int) $candidate->academic_year_id === (int) $academicYearId && $academicYearId)
                    + (int) ((int) $candidate->grade_id === (int) $gradeId && $gradeId);
            })
            ->first();
    }

    private function priceItemsOrLegacyFallback(EnrolmentPrice $price): Collection
    {
        if ($price->items->isNotEmpty()) {
            return $price->items
                ->where('is_active', true)
                ->where('is_required', true)
                ->values();
        }

        // Keep installations with legacy price rows usable until item data is configured.
        return collect([
            new EnrolmentPriceItem([
                'type' => 'enrolment',
                'name' => $price->name ?: 'Enrolment Fee',
                'amount' => $price->price,
                'is_required' => true,
                'is_active' => true,
            ]),
        ]);
    }

    private function headerData(array $data): array
    {
        $totalRequired = collect($data['items'])
            ->filter(function ($item) {
                return !empty($item['is_active']) && !empty($item['is_required']);
            })
            ->sum(function ($item) {
                return (float) $item['amount'];
            });

        return [
            'branch_id' => $data['branch'],
            'level_id' => $data['level'],
            'academic_year_id' => $data['academic_year_id'] ?? null,
            'grade_id' => $data['grade_id'] ?? null,
            'name' => $data['name'],
            'price' => $totalRequired,
            'type' => $data['type'],
            'is_active' => $data['is_active'] ?? 1,
        ];
    }

    private function syncItems(EnrolmentPrice $price, array $items): void
    {
        $price->items()->delete();
        foreach ($items as $item) {
            $price->items()->create([
                'type' => $item['type'],
                'name' => $item['name'],
                'amount' => $item['amount'],
                'is_required' => $item['is_required'] ?? false,
                'is_active' => $item['is_active'] ?? false,
            ]);
        }
    }
}
