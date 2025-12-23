<?php

namespace App\Services;

interface EnrolmentService
{
    public function get();
    public function show($id);
    public function showByCode($code, $with=[]);
    public function post($data);
    public function put($data);
    public function delete($id);
}
