<?php

namespace App\Services;

interface AdmissionStatementService {
    public function showCode($code);
    public function show($id);
    public function postStatement($data);
    public function postFinancial($data);
    public function getFinancial($id);
    public function postAgreement($data);
    public function getAgreement($id, $type);
    public function get($with = []);
    public function delete($id);
}
