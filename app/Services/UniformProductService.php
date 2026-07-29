<?php

namespace App\Services;

interface UniformProductService
{
    public function get();
    public function show($id);
    public function post($data);
    public function put($data);
    public function delete($id);
}
