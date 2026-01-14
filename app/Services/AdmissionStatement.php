<?php
namespace App\Services;

interface AdmissionStatementService {
    public function showCode($code);
    public function show($id);
    public function postStatement($data);
    public function get($with = []);
    public function delete($id);
}
