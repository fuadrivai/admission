<?php

namespace App\Services;

interface AdmissionService
{
    public function show($id);
    public function showByCode($code);
    public function getParent($child_id,$role);
    public function getApplicant($id);
    public function post($data);
    public function postParent($data);
    public function PostHealth($data);
}
