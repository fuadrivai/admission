<?php

namespace App\Services;

interface SchoolVisitService
{
    public function get();
    public function getByDateTime($request);
    public function show($id);
    public function showByCode($code);
    public function post($data);
    public function put($visit,$data);
    public function fetch($data);
    public function delete($id);
    public function maxCapacity();
    public function postMax($data);
}
