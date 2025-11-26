<?php

namespace App\Services;

interface EnrolmentPriceService
{
    public function get();
    public function show($id);
    public function getRegistrationPrice($branchId,$levelId);
    public function post($data);
    public function put($data);
    public function delete($id);
}
