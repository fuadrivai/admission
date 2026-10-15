<?php

namespace App\Http\Requests;

class StoreEnrolmentDpPublicRequest extends StoreEnrolmentDpRequest
{
    public function authorize()
    {
        return true;
    }

    protected function prepareForValidation()
    {
        $this->merge(['registration_type' => 'external']);
    }

    public function rules()
    {
        $rules = parent::rules();
        $rules['payment_types.*'] = ['in:enrolment,dp'];
        $rules['payment_types'] = 'required|array|min:1';
        unset($rules['amounts.other'], $rules['descriptions.other']);

        return $rules;
    }
}
