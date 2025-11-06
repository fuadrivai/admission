<?php

namespace App\Services;

interface HolidayService
{
    public function get();
    public function isHoliday($date);
    public function show($id);
    public function post($data);
    public function put($data);
    public function delete($id);
    public function nextHoliday();
}
