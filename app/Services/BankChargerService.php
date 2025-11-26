<?php

namespace App\Services;

interface BankChargerService
{
    public function get($with=[]);
    public function show($id,$with=[]);
    public function post($data);
    public function put($data);
    public function delete($id);
}
