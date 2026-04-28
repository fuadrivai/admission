<?php

namespace App\Services;

interface BranchService
{
    public function get($with=[]);
    public function getByName($name);
    public function show($id,$with=[]);
    public function post($data);
    public function put($data);
    public function delete($id);
}
