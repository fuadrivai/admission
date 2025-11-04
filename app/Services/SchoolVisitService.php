<?php

namespace App\Services;

interface SchoolVisitService
{
    public function get();
    public function show($id);
    public function post($data);
    public function put($data);
    public function fetch($data);
    public function delete($id);
}
