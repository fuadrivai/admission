<?php

namespace App\Services\Implement;

use App\Mail\AdmissionEmail;
use App\Models\Admission;
use App\Models\AdmissionDocument;
use App\Models\AdmissionStatement;
use App\Models\Applicant;
use App\Models\ApplicantParent;
use App\Models\Enrolment;
use App\Models\Health;
use App\Models\Immunizations;
use App\Models\MedicalHistory;
use App\Models\ParentDeclaration;
use App\Models\PregnancyHistory;
use App\Services\AdmissionService;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

use function App\Helpers\imageToBase64;
use function App\Helpers\normalizePhoneNumber;
use function App\Helpers\setupMail;

class AdmissionImplement implements AdmissionService
{
    public function show($id)
    {
        $admission  = Admission::with(
                [
                    'enrolment',
                    'branch',
                    'level',
                    'grade',
                    'applicant',
                    'applicant.health',
                    'applicant.immunization',
                    'applicant.medicalHistory',
                    'applicant.pregnancyHistory',
                    'applicant.declaration',
                    'applicant.parents',
                ]
            )->findOrFail($id);
            return $admission;
    }
    public function showByCode($code)
    {
        $admission =  Admission::with(['applicant','enrolment','branch','level.division','grade','statement'])
                        ->where('code', $code)->first();
        if (!$admission){
            $enrolment = Enrolment::with(['branch', 'grade', 'level'])->where('code', $code)->firstOrFail();
            if ($enrolment->payment_status == 'PAID') {
                $applicantData = [
                    'fullname' => $enrolment->child_name,
                    'date_of_birth'=>$enrolment->date_of_birth,
                    'place_of_birth'=>$enrolment->place_of_birth,
                    'previous_school'=>$enrolment->previous_school,
                    'zipcode'=>$enrolment->zipcode,
                    'address'=>$enrolment->address,
                    'parent_phone'=>$enrolment->phone_number,
                ];
                $applicant = Applicant::create($applicantData);

                $admissionData = [
                    'applicant_id' => $applicant->id,
                    'enrolment_id' => $enrolment->id,
                    'branch_id' => $enrolment->branch_id,
                    'level_id' => $enrolment->level_id,
                    'grade_id' => $enrolment->grade_id,
                    'accademic_year' => $enrolment->academic_year,
                    'code' => $enrolment->code,
                    'is_complete' => 0,
                ];
                $admission = Admission::create($admissionData);

                $parentData = [
                    'role'=>$enrolment->relationship,
                    'applicant_id'=>$applicant->id,
                    'fullname'=>$enrolment->parent_name,
                    'phone'=>$enrolment->phone_number,
                    'email'=>$enrolment->email,
                    'zipcode'=>$enrolment->zipcode,
                    'address'=>$enrolment->address,
                ];
                ApplicantParent::create($parentData);

                $admission->activities()->create([
                    'prospects_id' => $enrolment->prospects_id,
                    'note'=>"Student data record created on ". Carbon::now()->toDateTimeString(),
                ]);

                return Admission::with(['applicant','enrolment','branch','level','grade'])
                        ->where('code', $code)->first();
            }else if($enrolment->payment_status == 'PENDING'){
                throw new \Exception("Enrolment has not been paid.");
            } else if($enrolment->payment_status == 'EXPIRED'){
                throw new \Exception("Enrolment has been EXPIRED");
            } else{
                throw new \Exception("no code found in enrolment or admission");
            }
        }
        return $admission;
    }

    public function checkStatus($code)
    {
        $admission = Admission::where('code', $code)->first();
        if (!$admission) {
            return 'student';
        }
        if (!$admission->is_complete) {
            return 'student';
        }

        $documentCount = AdmissionDocument::where('admission_id', $admission->id)->count();
        if ($documentCount < 4) {
            return 'file';
        }

        $statement = AdmissionStatement::where('admission_id', $admission->id)->first();
        return ($statement && $statement->is_completed) ? 'success' : 'statement';
    }

    public function post($data)
    {
        return DB::transaction(function () use ($data) {

            $app = $data['applicant'] ?? [];

            $admission = Admission::where('code', $data['code'])
                ->with('applicant')
                ->first();

            if ($admission) {
                $admission->applicant->update(
                    $this->mapApplicantData($app)
                );

                $admission->update([
                    'level_id' => $data['level_id'] ?? $admission->level_id,
                    'grade_id' => $data['grade_id'] ?? $admission->grade_id,
                    'accademic_year' => $data['academic_year'] ?? $admission->accademic_year,
                    'is_complete' => 0,
                ]);
            } else {
                $applicant = Applicant::create(
                    $this->mapApplicantData($app)
                );

                $admission = Admission::create([
                    'applicant_id' => $applicant->id,
                    'enrolment_id' => $data['enrolment_id'] ?? null,
                    'branch_id' => $data['branch_id'] ?? null,
                    'level_id' => $data['level_id'] ?? null,
                    'grade_id' => $data['grade_id'] ?? null,
                    'accademic_year' => $data['academic_year'] ?? null,
                    'code' => $data['code'],
                    'is_complete' => 0,
                ]);
            }

            return Admission::with(['applicant','branch','level','grade'])->find($admission->id);
        });
    }

    public function getParent($child_id,$role){
        return ApplicantParent::where('applicant_id', $child_id)
            ->where('role', $role)
            ->firstOrFail();
    }

    public function postParent($data)
    {
        $parent = ApplicantParent::updateOrCreate(
            [
                'id' => $data['id'] ?? null,
            ],
            [
                'applicant_id'   => $data['applicant_id'] ?? null,
                'role'           => $data['role'] ?? null,
                'fullname'       => $data['fullname'] ?? null,
                'phone'          => $data['phone'] ?? null,
                'home_phone'     => $data['home_phone'] ?? null,
                'email'          => $data['email'] ?? null,
                'birth_place'    => $data['birth_place'] ?? null,
                'birth_date'     => $data['birth_date'] ?? null,
                'identity_number'=> $data['identity_number'] ?? null,
                'religion'       => $data['religion'] ?? null,
                'ethnicity'      => $data['ethnicity'] ?? null,
                'address'        => $data['address'] ?? null,
                'zipcode'        => $data['zipcode'] ?? null,
                'education'      => $data['education'] ?? null,
                'occupation'     => $data['occupation'] ?? null,
                'company_name'   => $data['company_name'] ?? null,
                'company_address'=> $data['company_address'] ?? null,
                'marital_status' => $data['marital_status'] ?? null,
                'monthly_income' => $data['monthly_income'] ?? 0,
            ]
        );

        return $parent;
    }

    public function getApplicant($id){
        return Applicant:: with([
            'health',
            'immunization',
            'medicalHistory',
            'pregnancyHistory',
            'declaration',
            'parents',
        ])->findOrFail($id);
    }

    public function PostHealth($data) {
        DB::transaction(function () use ($data) {
            $applicantId = $data['applicant']['id'];
            $admissionId = $data['id'];

            Immunizations::updateOrCreate(
                ['applicant_id' => $applicantId],
                $data['applicant']['immunization']
            );

            Health::updateOrCreate(
                ['applicant_id' => $applicantId],
                $data['applicant']['health']
            );

            PregnancyHistory::updateOrCreate(
                ['applicant_id' => $applicantId],
                $data['applicant']['pregnancy_history']
            );

            MedicalHistory::updateOrCreate(
                ['applicant_id' => $applicantId],
                $data['applicant']['medical_histories']
            );

            ParentDeclaration::updateOrCreate(
                ['applicant_id' => $applicantId],
                $data['applicant']['parent_declarations']
            );

            $admission = Admission::with(['applicant.parents','level','branch','grade'])->findOrFail($admissionId);

            $admission->update(['is_complete' => 1]);
            $admission->activities()->create([
                'prospects_id' => $admission->enrolment->prospects_id,
                'note'=>"Student data record completed on ". Carbon::now()->toDateTimeString(),
            ]);

            $pdfPath = $this->generateEnrolmentPdf($admission);

            $admission->subject  = 'Submitted Personal Information';
            $admission->template = 'email-template.student-information';

            setupMail($admission->branch_id);

            $emails = $admission->applicant->parents
                ->pluck('email')
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            if (!empty($emails)) {
                Mail::to($emails)->send(
                    (new AdmissionEmail($admission))
                        ->attach($pdfPath, [
                            'as'   => 'student-information-'.$admission->code.'.pdf',
                            'mime' => 'application/pdf',
                        ])
                );
            }

            $is_sendToPrincipal = $admission->applicant->therapy_history == 1|| $admission->applicant->ever_not_school==1 || $admission->applicant->dev_check;

            if ($is_sendToPrincipal) {
                $principal_email = $admission->level->email;
                if (!empty($principal_email)) {
                    $admission->subject  = 'Submitted Personal Information';
                    $admission->template = 'email-template.student-information';
                    Mail::to($principal_email)->send(
                        (new AdmissionEmail($admission))
                            ->attach($pdfPath, [
                                'as'   => 'student-information-'.$admission->code.'.pdf',
                                'mime' => 'application/pdf',
                            ])
                    );
                }
            }
        });

        return response()->json([
            'success' => true,
            'message' => 'Data kesehatan berhasil disimpan'
        ]);
    }


    private function mapApplicantData(array $app): array
    {
        return [
            'fullname' => $app['fullname'] ?? null,
            'nickname' => $app['nickname'] ?? null,
            'gender' => $app['gender'] ?? null,
            'date_of_birth' => $app['date_of_birth'] ?? null,
            'place_of_birth' => $app['place_of_birth'] ?? null,
            'age' => $app['age'] ?? null,

            'height' => isset($app['height']) && $app['height'] !== ''
                ? (float) $app['height'] : 0,
            'weight' => isset($app['weight']) && $app['weight'] !== ''
                ? (float) $app['weight'] : 0,

            'religion' => $app['religion'] ?? null,
            'ethnicity' => $app['ethnicity'] ?? null,
            'citizenship' => $app['citizenship'] ?? null,

            'siblings_count' => isset($app['siblings_count'])
                ? (int) $app['siblings_count'] : 0,
            'birth_order' => isset($app['birth_order'])
                ? (int) $app['birth_order'] : 0,

            'home_language' => $app['home_language'] ?? null,
            'other_languages' => $app['other_languages'] ?? null,
            'address' => $app['address'] ?? null,
            'zipcode' => $app['zipcode'] ?? null,
            'home_phone' => $app['home_phone'] ?? null,

            'parent_phone' => !empty($app['parent_phone'])
                ? normalizePhoneNumber($app['parent_phone'])
                : null,

            'living_with' => $app['living_with'] ?? null,
            'living_with_other' => $app['living_with_other'] ?? null,

            'distance_km' => isset($app['distance_km']) && $app['distance_km'] !== ''
                ? (float) $app['distance_km'] : 0,

            'previous_school' => $app['previous_school'] ?? null,
            'previous_school_address' => $app['previous_school_address'] ?? null,
            'graduation_year' => $app['graduation_year'] ?? null,

            'ever_not_school' => $app['ever_not_schooling'] == "true"? 1:0,
            'not_school_duration' => $app['not_school_duration'] ?? null,
            'not_school_reason' => $app['not_school_reason'] ?? null,

            'dev_check' => $app['dev_checked']=="true" ? 1 : 0,
            'dev_diagnosis' => $app['dev_diagnosis'] ?? null,

            'therapy_history' => $app['therapy_history'] == "true" ? 1 : 0,
            'therapy_detail' => $app['therapy_detail'] ?? null,

            'emergency_contact_priority' => $app['emergency_contact_priority'] ?? null,
            'emergency_contact_phone' => $app['emergency_contact_phone'] ?? null,
        ];
    }

    private function generateEnrolmentPdf($admission)
    {
        $parents = $admission->applicant->parents->keyBy('role');
        $father   = $parents->get('father');
        $mother   = $parents->get('mother');
        $guardian = $parents->get('guardian');
        $logoPath   = public_path('assets/images/Logo-all-branch.png');
        $imageBase64 = imageToBase64($logoPath);

        $pdf = Pdf::loadView('pdf.student-enrolment', [
                'data'     => $admission,
                'logo'     => $imageBase64,
                'father'   => $father,
                'mother'   => $mother,
                'guardian' => $guardian,
            ])
            ->setPaper('a4', 'portrait')
            ->setWarnings(false);

        $path = 'admission/enrolment/student-' . $admission->code . '.pdf';

        Storage::put($path, $pdf->output());

        return storage_path('app/' . $path);
    }

}
