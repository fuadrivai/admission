<?php

namespace App\Services;

interface AdmissionService
{
    public function showByCode($code);
    public function post($data);
}
