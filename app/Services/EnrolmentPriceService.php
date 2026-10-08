<?php

namespace App\Services;

use Illuminate\Support\Collection;

interface EnrolmentPriceService
{
    public function get($with = [], $branchId = null);
    public function show($id);
    public function getRegistrationPrice($branchId, $levelId, $academicYearId = null, $gradeId = null);
    public function getRequiredPriceItems($academicYearId, $branchId, $levelId, $gradeId): Collection;
    public function post($data);
    public function put($data);
    public function delete($id);
}
