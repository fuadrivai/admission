<?php

namespace App\Services;

interface ProspectService
{
    public function get($with=[]);
    public function getbyCode($code, $with=[]);
    public function show($id,$with=[]);
    public function post($data);
    public function put($data);
    public function delete($id);
}
