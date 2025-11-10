<?php

namespace App\Services;

interface WhatsappCodeService
{
    public function get($data=[]);
    public function show($id,$data=[]);
    public function post($data);
    public function put($data);
    public function delete($id);
}
