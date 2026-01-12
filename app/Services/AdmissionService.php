<?php

namespace App\Services;

interface AdmissionService
{
    public function showByCode($code);
    public function getParent($child_id,$role);
    public function post($data);
    public function postParent($data);
}
