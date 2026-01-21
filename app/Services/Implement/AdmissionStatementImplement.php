<?php

namespace App\Services\Implement;

use App\Models\AdmissionStatement;
use App\Models\FinancialAgreement;
use App\Models\StatementAgreement;
use App\Services\AdmissionStatementService;
use Carbon\Carbon;

class AdmissionStatementImplement implements AdmissionStatementService{

    public function showCode($code){}
    public function get($with = []){}
    public function postStatement($data){
        $statement = 
        AdmissionStatement::updateOrCreate(
            [
                'id' => $data['id'],
            ],
            [
                'admission_id' => $data['admission_id'], 
                'actor' => $data['actor'],
                'identity_number' => $data['identity_number'],
                'fullname' => $data['fullname'],
                'is_completed' => $data['is_completed']==true?1:0,
                'complete_at' => $data['is_completed']==true?Carbon::now():null,
            ]
        );

        $statement->load('financial');
        return $statement;
    }
    public function postFinancial($data){
        $financial = 
        FinancialAgreement::updateOrCreate(
            [
                'id' => $data['id'],
            ],
            [
                'admission_statement_id' => $data['admission_statement_id'], 
                'agree_full_payment_terms' => $data['agree_full_payment_terms']==true?1:0,
                'development_fee' => $data['development_fee'],
                'annual_fee' => $data['annual_fee'],
                'school_fee' => $data['school_fee'],
                'agree_development_fee_policy' => $data['agree_development_fee_policy']==true?1:0,
                'agree_annual_and_school_fee_policy' => $data['agree_annual_and_school_fee_policy']==true?1:0,
                'agree_exam_fee' => $data['agree_exam_fee']==true?1:0,
                'agree_learning_material_fee' => $data['agree_learning_material_fee']==true?1:0,
                'agree_exschool_fee' => $data['agree_exschool_fee']==true?1:0,
                'agree_additional_activity_fee' => $data['agree_additional_activity_fee']==true?1:0,
                'agree_monthly_school_fee_payment' => $data['agree_monthly_school_fee_payment']==true?1:0,
                'agree_ittihada_fee' => $data['agree_ittihada_fee']==true?1:0,
                'agree_full_financial_obligation' => $data['agree_full_financial_obligation']==true?1:0,
                'agree_financial_terms_and_consequences' => $data['agree_financial_terms_and_consequences']==true?1:0,
                'agree_truth_and_consent' => $data['agree_truth_and_consent']==true?1:0,
                'agreed_at' => Carbon::now(),
                'ip_address' => $data['ip_address']??null,
                'user_agent' => $data['user_agent']??null,
            ]
        );

        return $financial;
    }
    public function getFinancial($id){
        return FinancialAgreement::where('id',$id)->first();
    }

    public function postAgreement($data){

        $agreement =  StatementAgreement::updateOrCreate(
            [
                'admission_statement_id' => $data['admission_statement_id'],
                'type' => $data['type'],
            ],
            [
                'agreed' => $data['agreed']==true?1:0,
                'agreed_at' => Carbon::now(),
                'ip_address' => $data['ip_address']??null,
                'user_agent' => $data['user_agent']??null,
            ]
        );
        return $agreement;
    }

    public function getAgreement($id,$type){
        return StatementAgreement::where('admission_statement_id', $id)
            ->where('type', $type)
            ->firstOrFail();
    }


    public function delete($id){}
    public function show($id){}

}
