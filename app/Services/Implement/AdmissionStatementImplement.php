<?php

namespace App\Services\Implement;

use App\Models\Admission;
use App\Models\AdmissionStatement;
use App\Services\AdmissionStatementService;

class AdmissionStatementImplement implements AdmissionStatementService{

    public function showCode($code){}
    public function get($with = []){}
    public function postStatement($data){
        $statement = AdmissionStatement :: where('admission_id',$data['admission_id'])->first();
        if ($statement) {
            # code...
        }
    }
    public function delete($id){}
    public function show($id){}

}
