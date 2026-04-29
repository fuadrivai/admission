<?php

namespace App\Services\Implement;

use App\Mail\AdmissionEmail;
use App\Models\Branch;
use App\Models\EmailSetting;
use App\Models\Enrolment;
use App\Models\Grade;
use App\Models\Level;
use App\Services\AcademicYearService;
use App\Services\BankChargerService;
use App\Services\BranchService;
use App\Services\EnrolmentPriceService;
use App\Services\EnrolmentService;
use App\Services\GradeService;
use App\Services\LevelService;
use App\Services\ProspectService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

use function App\Helpers\codeGenerator;
use function App\Helpers\createXenditInvoice;
use function App\Helpers\generate;
use function App\Helpers\normalizePhoneNumber;

class EnrolmentImplement implements EnrolmentService
{

    protected $prospectService;
    protected $enrolmentPriceService;
    protected $bankChargerService;
    protected $branchService;
    protected $levelService;
    protected $gradeService;
    protected $academicYearService;

    public function __construct(
        ProspectService $prospectService,
        EnrolmentPriceService $enrolmentPriceService,
        BankChargerService $bankChargerService,
        BranchService $branchService,
        LevelService $levelService,
        GradeService $gradeService,
        AcademicYearService $academicYearService
        )
    {
        $this->prospectService = $prospectService;
        $this->enrolmentPriceService = $enrolmentPriceService;
        $this->bankChargerService = $bankChargerService;
        $this->branchService = $branchService;
        $this->levelService = $levelService;
        $this->gradeService = $gradeService;
        $this->academicYearService = $academicYearService;
    }

    public function get($with=[])
    {
        return Enrolment::with($with)->get();
    }

    public function show($id, $with=[])
    {
        return Enrolment::with($with)->findOrFail($id);
    }

    public function postForm($request)
    {
        
        $data = [
            'prospects_id'             => null,
            'already_visit'            => 0,
            'code'                     => null,
            'is_current_student'       => "no",
            'student_branch'           => null,
            'mhis_portal_username'     => null,
            'branch_id'                => null,
            'level_id'                 => null,
            'grade_id'                 => null,
            'academic_year'            => $request->academicYear,
            'academic_year_id'         => null,
            'parent_name'              => $request->parentName,
            'email'                    => $request->email,
            'phone_number'             => normalizePhoneNumber($request->phone),
            'relationship'             => "",
            'zipcode'                  => null,
            'address'                  => "",
            'child_name'               => $request->childName,
            'place_of_birth'           => "",
            'date_of_birth'            => Carbon::now()->format('Y-m-d'),
            'current_school'           => "",
            'child_sosmed'             => null,
            'open_day_visited'         => 0,
            'knowledge_about_program'  => "",
            'info_from'                => "",
            'info_from_message'        => null,
            'reason_for_enrolment'     => "",
            'preferred_program'        => "",
            'expectation_mhis_impact'  => "",
            'trust_reason'             => null,
            'recommender_name'         => null,
            'recommender_phone'        => null,
            'recommender_child_name'   => null,
            'recommender_child_class'  => null,
            'payment_date'             => null,
            'source_data'              => $request->source,
            'regis_place'              => $request->place,
            'data_from'                => "custom_form",
        ];

        $strLevel = $this->resolveLevel($request->level);

        $branch = $this->branchService->getByName("bintaro");
        $data['branch_id'] = $branch->id;

        $level = $this->levelService->getByBranch($branch->id, $strLevel)->firstWhere('name', $strLevel);
        $data['level_id'] = $level->id;

        $grade = $this->gradeService->byLevelId($level->id)->first();
        $data['grade_id'] = $grade->id;

        $ay = $this->academicYearService->byName($request->academicYear);
        $data['academic_year_id'] = $ay->id;

        $bank = $this->bankChargerService->get();

        $enrolForm = $this->enrolmentPriceService->getRegistrationPrice($data['branch_id'],$data['level_id']);
        $data['bank_charger'] = $bank->price;
        
        // $data['registration_fee'] = $enrolForm->price;
        $parseValue= $this->calculateSeatAndForm($request, $enrolForm->price);
        $data['registration_fee'] = $parseValue['registration_form'];

        
        $data['custom_payment'] = $parseValue['seat_rsvp'] > 0 ? $parseValue['seat_rsvp']:$parseValue['full_payment'];
        $data['discount'] = $parseValue['registration_discount'];
        $data['amount_paid'] = $data['bank_charger'] + $data['registration_fee'] + $data['custom_payment'];
        $data['invoice_id'] =  codeGenerator('enrolments','invoice_id','INV-ENROL');
        $data['noted'] = "Enrolment created with custom form input: " . $request->option . " resulting in custom form value: " . $data['custom_payment']. " academic year: " . $request->academicYear . " with discount applied: " . $parseValue['registration_discount'];

        $level_name = "";
        $grade_name = Grade::find($data['grade_id'])->name;


        if ($data['already_visit'] == 0) {
            $level = Level::find($data['level_id']);
            $level_name = $level->name;
            $data['code'] = generate($level->branch_code);
            $prospects = $this->saveProspects($data);
            $data['prospects_id'] = $prospects->id;
        }else{
            $data['code'] = $request->code;
            if(!isset($request->prospectsId) || is_null($request->prospectsId) || empty($request->prospectsId) || $request->prospectsId == ''){
                $prospects = $this->saveProspects($data);   
                $data['prospects_id'] = $prospects->id;
            }else{
                $data['prospects_id'] = $request->prospectsId;
            }
        }

        $payload = [
            "external_id"=> $data['invoice_id'],
            "amount"=> $data['amount_paid'],
            "payer_email"=> $data['email'],
            "description"=> "Enrolment payment -". $data['child_name'] . " for " . $data['academic_year'] . " - " . $level_name . " " . $grade_name,
            "invoice_duration"=> (60*60*24*7)
        ];
        $xendit = createXenditInvoice($payload);
        $data['payment_status'] = $xendit['status'];
        $data['payment_url'] = $xendit['invoice_url'];
        $data['create_va_date'] =  Carbon::parse($xendit['created']);
        $data['expiry_va_date'] = Carbon::parse($xendit['expiry_date']);
        $enrolment = Enrolment::create($data);

        $enrolment->activities()->create([
            'prospects_id' => $enrolment->prospects_id,
            'note'=>"Enrolment created with invoice ID " . $enrolment->invoice_id . " and payment status: " . $enrolment->payment_status,
        ]);

        $enrolment['subject'] = "Enrolment Payment of $enrolment->child_name - Mutiara Harapan Islamic School";
        $enrolment['template'] = 'email-template.enrolment-event';
        $enrolment['level_name'] = $level_name;
        $enrolment['grade_name'] = $grade_name??"";
        $enrolment['option'] = $data['custom_payment'] > 0 ? "Seat Reservation": "Full Payment";

        $setting = EmailSetting::where('branch_id',$data['branch_id'])->first();
        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp', [
            'transport' => $setting->mailer,
            'host' => $setting->host,
            'port' => $setting->port,
            'encryption' => $setting->encryption,
            'username' => $setting->username,
            'password' => $setting->app_password,
            'timeout' => null,
        ]);
        Config::set('mail.from', [
            'address' => $setting->from_address,
            'name' => $setting->from_name,
        ]);

        Mail::to($enrolment->email)->send(new AdmissionEmail($enrolment));

        return $enrolment;
    }

    public function post($request)
    {

        $data = [
            'prospects_id'             => null,
            'already_visit'            => $request->alreadyVisit === 'true'?1:0,
            'code'                     => null,
            'is_current_student'       => $request->isCurrentStudent,
            'student_branch'           => $request->studentBranch,
            'mhis_portal_username'     => $request->mhisPortalUsername,
            'branch_id'                => $request->branch,
            'level_id'                 => $request->level,
            'grade_id'                 => $request->grade,
            'academic_year_id'         => $request->academicYearId,
            'academic_year'            => $request->academicYear,
            'parent_name'              => $request->parentName,
            'email'                    => $request->email,
            'phone_number'             => normalizePhoneNumber($request->phone),
            'relationship'             => $request->relationship,
            'zipcode'                  => $request->zipCode,
            'address'                  => $request->address,
            'child_name'               => $request->childName,
            'place_of_birth'           => $request->placeOfBirth,
            'date_of_birth'            => $request->dateOfBirth,
            'current_school'           => $request->currentSchool,
            'child_sosmed'             => $request->childSosmed,
            'open_day_visited'         => $request->opendayVisited === 'true' ? 1 : 0,
            'knowledge_about_program'  => $request->knowledgeAboutProgram,
            'info_from'                => $request->infoFrom,
            'info_from_message'        => $request->infoFromMessage,
            'reason_for_enrolment'     => $request->reasonForEnrolment,
            'preferred_program'        => $request->prefferedProgram,
            'expectation_mhis_impact'  => $request->expectationMhisImpact,
            'trust_reason'             => null,
            'recommender_name'         => $request->recommenderName,
            'recommender_phone'        => $request->recommenderPhone,
            'recommender_child_name'   => $request->recommenderChildName,
            'recommender_child_class'  => $request->recommenderChildClass,
            'payment_date'             => null,
            'source_data'              => $request->isCurrentStudent == "yes"? "internal":"external",
            'regis_place'              => null,
            'data_from'                => "web_form",
        ];

        $bank = $this->bankChargerService->get();

        $enrolForm = $this->enrolmentPriceService->getRegistrationPrice($data['branch_id'],$data['level_id']);
        $data['bank_charger'] = $bank->price;
        $data['registration_fee'] = $enrolForm->price;
        $data['amount_paid'] = $data['bank_charger'] + $data['registration_fee'];
        $data['invoice_id'] =  codeGenerator('enrolments','invoice_id','INV-ENROL');
        $level_name = "";
        $grade_name = Grade::find($data['grade_id'])->name;

        if ($data['already_visit'] == 0) {
            $level = Level::find($data['level_id']);
            $level_name = $level->name;
            $data['code'] = generate($level->branch_code);
            $prospects = $this->saveProspects($data);
            $data['prospects_id'] = $prospects->id;
        }else{
            $data['code'] = $request->code;
            if(!isset($request->prospectsId) || is_null($request->prospectsId) || empty($request->prospectsId) || $request->prospectsId == ''){
                $prospects = $this->saveProspects($data);   
                $data['prospects_id'] = $prospects->id;
            }else{
                $data['prospects_id'] = $request->prospectsId;
            }
        }

        $payload = [
            "external_id"=> $data['invoice_id'],
            "amount"=> $data['amount_paid'],
            "payer_email"=> $data['email'],
            "description"=> "Enrolment payment -". $data['child_name'] . " for " . $data['academic_year'] . " - " . $level_name . " " . $grade_name,
            "invoice_duration"=> (60*60*24*7)
        ];
        $xendit = createXenditInvoice($payload);
        $data['payment_status'] = $xendit['status'];
        $data['payment_url'] = $xendit['invoice_url'];
        $data['create_va_date'] =  Carbon::parse($xendit['created']);
        $data['expiry_va_date'] = Carbon::parse($xendit['expiry_date']);
        $enrolment = Enrolment::create($data);

        $enrolment->activities()->create([
            'prospects_id' => $enrolment->prospects_id,
            'note'=>"Enrolment created with invoice ID " . $enrolment->invoice_id . " and payment status: " . $enrolment->payment_status,
        ]);

        $enrolment['subject'] = "Enrolment Payment of $enrolment->child_name - Mutiara Harapan Islamic School";
        $enrolment['template'] = 'email-template.enrolment';
        $enrolment['level_name'] = $level_name;
        $enrolment['grade_name'] = $grade_name??"";
        $setting = EmailSetting::where('branch_id',$data['branch_id'])->first();
        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp', [
            'transport' => $setting->mailer,
            'host' => $setting->host,
            'port' => $setting->port,
            'encryption' => $setting->encryption,
            'username' => $setting->username,
            'password' => $setting->app_password,
            'timeout' => null,
        ]);
        Config::set('mail.from', [
            'address' => $setting->from_address,
            'name' => $setting->from_name,
        ]);

        Mail::to($enrolment->email)->send(new AdmissionEmail($enrolment));
        return $enrolment;
    }

    public function showByCode($code, $with = [])
    {
        return Enrolment::with($with)->where('code', $code)->firstOrFail();
    }

    public function put($data)
    {
        
    }

    public function delete($id)
    {
        $enrolment = Enrolment::findOrFail($id);
        return $enrolment->delete();
    }

    function saveProspects($value){
        $dataProspect = [
            'code'          => $value['code'],
            'child_name'    => $value['child_name'],
            'date_of_birth' => $value['date_of_birth'],
            'place_of_birth'=> $value['place_of_birth'],
            'current_school'=> $value['current_school'],
            'parent_name'   => $value['parent_name'],
            'email'         => $value['email'],
            'phone_number'  => $value['phone_number'],
            'zipcode'       => $value['zipcode'],
            'address'       => $value['address'],
            'relationship'  => $value['relationship'],
            'source_module' => 'enrolment',
        ];
        return $this->prospectService->post($dataProspect);
    }

    private function resolveLevel($grade)
    {
        $map = [
            'Playgroup' => [
                'Playgroup B',
                'Playgroup C',
            ],
            'Kindergarten' => [
                'Kindergarten A',
                'Kindergarten B',
            ],
            'Primary' => [
                'Primary',
            ],
            'Lower Secondary' => [
                'Lower Secondary',
            ],
            'Upper Secondary' => [
                'Upper Secondary',
            ],
            'Preschool Development Class' => [
                'Playgroup B Development Class',
                'Playgroup C Development Class',
                'Kindergarten A Development Class',
                'Kindergarten B Development Class',
            ],
            'Primary Development Class' => [
                'Primary Development Class',
            ],
            'Junior High Development Class' => [
                'JH Development Class',
            ],
        ];

        foreach ($map as $level => $grades) {
            if (in_array($grade, $grades)) {
                return $level;
            }
        }

        return null; // kalau ga ketemu
    }

    private function parseOption($option,$registration_form)
    {
        $registration = 0;
        $seat = 0;
        $fullPayment = 0;

        // Case: ada separator |
        if (str_contains($option, '|')) {
            [$label, $amount] = explode('|', $option);
            $amount = (int) $amount;

            if (str_contains(strtolower($label), 'registration')) {
                // Registration + Seat
                $registration = $registration_form ?? 0;
                $seat = $amount-$registration;
            } else {
                // Seat only
                $registration = 0;
                $seat = $amount;
            }
        } else {
            // Case: angka full
            $amount = (int) $option;

            $registration = $registration_form ?? 0;
            $fullPayment = $amount - $registration;
        }

        return [
            'registration_form' => $registration,
            'seat_rsvp' => $seat,
            'full_payment' => $fullPayment,
        ];
    }

    private function applyRegistrationDiscount($registration, $place, $academicYear,$level)
    {
        $discount = 0;
        $streamingTest = 850000;
        $discountAmount = 0;

        if ($registration <= 0) {
            return [
                'original' => 0,
                'discount' => 0,
                'final' => 0,
            ];
        }

        if ($place === 'Exhibition' && str_contains($level, 'Development Class')) {
            $discount = 0.5;
        } elseif ($place === 'Exhibition') {
            if ($academicYear === '2026/2027') {
                $discount = 0.5;
            } elseif ($academicYear === '2027/2028') {
                $discount = 1;
            }
        }

        if ($level === "Upper Secondary") {
            $registration = $registration -  $streamingTest;
            $discountAmount = $registration * $discount;
            $registration = $registration + $streamingTest;
        }else{
            $discountAmount = $registration * $discount;
        }

        return [
            'original' => $registration,
            'discount' => $discountAmount,
            'final' => ($registration - $discountAmount),
        ];
    }

    private function calculateSeatAndForm($request,$registration_form){

        // Step 1: parse option
        $parsed = $this->parseOption($request['option'],$registration_form);

        // Step 2: apply discount
        $registrationDiscount = $this->applyRegistrationDiscount(
            $parsed['registration_form'],
            $request['place'],
            $request['academicYear'],
            $request['level'],
        );

        // Final result
        $result = [
            'registration_form' => $registrationDiscount['final'],
            'registration_discount' => $registrationDiscount['discount'],
            'seat_rsvp' => $parsed['seat_rsvp'],
            'full_payment' => $parsed['full_payment'],
        ];

        return $result;
    }
}
