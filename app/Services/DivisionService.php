<?php

namespace App\Services;

interface DivisionService
{
    public function get();
    public function show($id);
    public function post($data);
    public function put($data);
    public function delete($id);
    public function paginate($int);
}
