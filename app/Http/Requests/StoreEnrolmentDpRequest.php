<?php

namespace App\Http\Requests;

use App\Models\Enrolment;
use App\Models\RegistrationPlace;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEnrolmentDpRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && auth()->user()->role !== 'user';
    }

    public function rules()
    {
        $rules = [
            'request_key' => 'required|uuid',
            'already_enrolment' => 'required|in:yes,no',
            'registration_type' => 'required|in:internal,external',
            'registration_place' => ['required', Rule::exists('registration_places', 'code')->where('is_active', true)],
            'registration_place_other' => [
                Rule::requiredIf(fn () => RegistrationPlace::where('code', $this->input('registration_place'))->where('is_other', true)->exists()),
                'nullable', 'string', 'max:255',
            ],
            'payment_types' => 'nullable|array',
            'payment_types.*' => [Rule::in(['enrolment', 'dp', 'other'])],
            'amounts.dp' => 'nullable|numeric|min:0.01|max:9999999999.99',
            'amounts.other' => 'nullable|numeric|min:0.01|max:9999999999.99',
            'descriptions.other' => 'nullable|string|max:255',
        ];

        if ($this->input('already_enrolment') === 'yes') {
            $rules['enrolment_code'] = 'required|string|exists:enrolments,code';
        } else {
            $rules += [
                'parent_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone_number' => 'required|string|max:20',
                'student_name' => 'required|string|max:255',
                'academic_year_id' => 'required|integer|exists:academic_years,id',
                'branch_id' => 'required|integer|exists:branches,id',
                'level_id' => 'required|integer|exists:levels,id',
                'grade_id' => 'required|integer|exists:grades,id',
            ];
        }

        return $rules;
    }

    public function messages()
    {
        return [
            'enrolment_code.exists' => 'Enrolment code not found. Please check the code and try again.',
            'registration_place_other.required' => 'Please specify the registration place.',
            'amount.required_if' => 'Enter a payment amount for DP or Other.',
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->input('already_enrolment') !== 'yes' || !$this->input('enrolment_code')) {
                return;
            }

            $enrolment = Enrolment::where('code', trim($this->input('enrolment_code')))->first();
            if ($enrolment && !$enrolment->hasPaidRegistrationPayment()) {
                $paymentStatus = strtoupper((string) $enrolment->payment_status);
                $message = $paymentStatus === 'EXPIRED'
                    ? 'This enrolment has expired. Select "No, new parent" to create a new enrolment.'
                    : ($paymentStatus === 'PENDING'
                        ? 'This enrolment payment is still pending. Complete the existing payment before creating another enrolment.'
                        : 'This enrolment has not completed its registration payment. Please use the existing enrolment payment process first.');

                $validator->errors()->add(
                    'enrolment_code',
                    $message
                );
            }

            if (in_array('enrolment', (array) $this->input('payment_types', []), true)) {
                $validator->errors()->add(
                    'payment_types',
                    'Registration Fee cannot be charged again to an existing enrolment.'
                );
            }
        });

        $validator->after(function ($validator) {
            $paymentTypes = (array) $this->input('payment_types', []);
            foreach (['dp', 'other'] as $type) {
                if (in_array($type, $paymentTypes, true) && (float) $this->input("amounts.{$type}") <= 0) {
                    $validator->errors()->add("amounts.{$type}", 'Enter an amount greater than zero.');
                }
            }
        });
    }
}
