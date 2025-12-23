<?php

namespace App\Services\Implement;

use App\Models\Admission;
use App\Models\Applicant;
use App\Services\AdmissionService;
use Illuminate\Support\Facades\DB;

use function App\Helpers\normalizePhoneNumber;

class AdmissionImplement implements AdmissionService
{
    public function showByCode($code)
    {
        return Admission::with(['applicant','enrolment','branch','level','grade'])->where('code', $code)->firstOrFail();
    }

    public function post($data)
    {

        return DB::transaction(function () use ($data) {

            $app = isset($data['applicant']) ? $data['applicant'] : [];
    
            $applicantData = [
                'fullname' => $app['fullname'] ?? null,
                'nickname' => $app['nickname'] ?? null,
                'gender' => $app['gender'] ??null,
                'date_of_birth' => $app['date_of_birth'] ?? null,
                'place_of_birth' => $app['place_of_birth'] ?? null,
                'age' => $app['age'] ?? null,
                'height' => isset($app['height']) && $app['height'] !== null && $app['height'] !== '' ? (float) $app['height'] : 0,
                'weight' => isset($app['weight']) && $app['weight'] !== null && $app['weight'] !== '' ? (float) $app['weight'] : 0,
                'religion' => $app['religion'] ?? null,
                'ethnicity' => $app['ethnicity'] ?? null,
                'citizenship' => $app['citizenship'] ?? null,
                'siblings_count' => isset($app['siblings_count']) ? (int) $app['siblings_count'] : 0,
                'birth_order' => isset($app['birth_order']) ? (int) $app['birth_order'] : 0,
                'home_language' => $app['home_language'] ?? null,
                'other_languages' => $app['other_languages'] ?? null,
                'address' => $app['address'] ?? null,
                'zipcode' => $app['zipcode'] ?? null,
                'home_phone' => $app['home_phone'] ?? null,
                'parent_phone' => isset($app['parent_phone']) ? normalizePhoneNumber($app['parent_phone']) : null,
                'living_with' => $app['living_with'] ?? null,
                'living_with_other' => $app['living_with_other'] ?? null,
                'distance_km' => isset($app['distance_km']) && $app['distance_km'] !== null && $app['distance_km'] !== '' ? (float) $app['distance_km'] : 0,
                'previous_school' => $app['previous_school'] ?? null,
                'previous_school_address' => $app['previous_school_address'] ?? null,
                'graduation_year' => $app['graduation_year'] ?? null,
                'ever_not_school' => (isset($app['ever_not_schooling']) && ($app['ever_not_schooling'] === 'true' || $app['ever_not_schooling'] === true || $app['ever_not_schooling'] === 1)) ? 1 : 0,
                'not_school_duration' => $app['not_school_duration'] ?? null,
                'not_school_reason' => $app['not_school_reason'] ?? null,
                'dev_check' => (isset($app['dev_checked']) && ($app['dev_checked'] === 'true' || $app['dev_checked'] === true || $app['dev_checked'] === 1)) ? 1 : 0,
                'dev_diagnosis' => $app['dev_diagnosis'] ?? null,
                'therapy_history' => (isset($app['therapy_history']) && ($app['therapy_history'] === 'true' || $app['therapy_history'] === true || $app['therapy_history'] === 1)) ? 1 : 0,
                'therapy_detail' => $app['therapy_detail'] ?? null,
                'emergency_contact_priority' => $app['emergency_contact_priority'] ?? null,
            ];
    
            $applicant = Applicant::create($applicantData);
    
            $admissionData = [
                'applicant_id' => $applicant->id,
                'enrolment_id' => $data['enrolment_id'] ?? $data['enrollment_id'] ?? null,
                'branch_id' => $data['branch_id'] ?? null,
                'level_id' => $data['level_id'] ?? null,
                'grade_id' => $data['grade_id'] ?? null,
                'accademic_year' => $data['academic_year'] ?? $data['academicYear'] ?? null,
                'code' => $data['code'] ?? null,
            ];
    
            $admission = Admission::create($admissionData);
    
            return Admission::with('applicant')->find($admission->id);
        });
    }
}
