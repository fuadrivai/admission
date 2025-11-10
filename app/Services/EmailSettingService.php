<?php

namespace App\Services;

interface EmailSettingService
{
    public function get($with=[]);
    public function show($id,$with=[]);
    public function post($data);
    public function put($data);
    public function delete($id);
}
