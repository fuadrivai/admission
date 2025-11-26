<?php

namespace App\Services\Implement;

use App\Models\EnrolmentPrice;
use App\Services\EnrolmentPriceService;

class EnrolmentPriceImplement implements EnrolmentPriceService
{
    public function get($with=[])
    {
        return EnrolmentPrice::with($with)->get();
    }

    public function show($id, $with=[])
    {
        return EnrolmentPrice::with($with)->findOrFail($id);
    }

    public function post($data)
    {
        $enrolmentPrice = EnrolmentPrice::create([
            'branch_id'     => $data['branch'],
            'level_id'     => $data['level'],
            'name'     => $data['name'],
            'price'     => $data['price'],
            'type'     => $data['type'],
            'is_active'     => $data['is_active']??1,
        ]);

        return $enrolmentPrice->load(['branch', 'level']);
    }

    public function put($data)
    {
        $enrolmentPrice = EnrolmentPrice::findOrFail($data['id']);

        $enrolmentPrice->update([
            'branch_id'     => $data['branch'],
            'level_id'     => $data['level'],
            'name'     => $data['name'],
            'price'     => $data['price'],
            'type'     => $data['type'],
            'is_active'     => $data['is_active']??1,
        ]);

        return $enrolmentPrice->load(['branch', 'level']);
    }

    public function delete($id)
    {
        $enrolmentPrice = EnrolmentPrice::findOrFail($id);
        return $enrolmentPrice->delete();
    }

    public function getRegistrationPrice($branchId,$levelId)
    {
        $enrolmentPrice = EnrolmentPrice::where('branch_id',$branchId)
                                            ->where('level_id',$levelId)
                                            ->where('type','form')
                                            ->where('is_active',1)->first();
        return $enrolmentPrice;
    }
}
