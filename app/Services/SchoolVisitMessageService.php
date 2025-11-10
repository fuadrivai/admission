<?php

namespace App\Services;

interface SchoolVisitMessageService
{
    public function get($data=[]);
    public function show($id,$data=[]);
    public function post($data);
    public function put($data);
    public function delete($id);
}
