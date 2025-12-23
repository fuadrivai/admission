<?php

namespace App\Services\Implement;

use App\Mail\AdmissionEmail;
use App\Models\EmailSetting;
use App\Models\Enrolment;
use App\Models\Level;
use App\Services\BankChargerService;
use App\Services\EnrolmentPriceService;
use App\Services\EnrolmentService;
use App\Services\ProspectService;
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

    public function __construct(
        ProspectService $prospectService,
        EnrolmentPriceService $enrolmentPriceService,
        BankChargerService $bankChargerService
        )
    {
        $this->prospectService = $prospectService;
        $this->enrolmentPriceService = $enrolmentPriceService;
        $this->bankChargerService = $bankChargerService;
    }

    public function get($with=[])
    {
        return Enrolment::with($with)->get();
    }

    public function show($id, $with=[])
    {
        return Enrolment::with($with)->findOrFail($id);
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
        ];

        $bank = $this->bankChargerService->get();

        $enrolForm = $this->enrolmentPriceService->getRegistrationPrice($data['branch_id'],$data['level_id']);
        $data['bank_charger'] = $bank->price;
        $data['registration_fee'] = $enrolForm->price;
        $data['amount_paid'] = $data['bank_charger'] + $data['registration_fee'];
        $data['invoice_id'] =  codeGenerator('enrolments','invoice_id','INV-ENROL');

        if ($data['already_visit'] == 0) {
            $level = Level::find($data['level_id']);
            $code = generate($level->branch_code);
            $dataProspect = [
                'code'          => $code,
                'child_name'    => $data['child_name'],
                'date_of_birth' => $data['date_of_birth'],
                'place_of_birth'=> $data['place_of_birth'],
                'current_school'=> $data['current_school'],
                'parent_name'   => $data['parent_name'],
                'email'         => $data['email'],
                'phone_number'  => $data['phone_number'],
                'zipcode'       => $data['zipcode'],
                'address'       => $data['address'],
                'relationship'  => $data['relationship'],
                'source_module' => 'enrolment',
            ];
            $prospects = $this->prospectService->post($dataProspect);
            $data['prospects_id'] = $prospects->id;
            $data['code'] = $code;
        }else{
            $data['code'] = $request->code;
            $data['prospects_id'] = $request->prospectsId;
        }
        $payload = [
            "external_id"=> $data['invoice_id'],
            "amount"=> $data['amount_paid'],
            "payer_email"=> $data['email'],
            "description"=> "Enrolment payment -". $data['child_name'],
            "invoice_duration"=> (60*60*24*3)
        ];
        $xendit = createXenditInvoice($payload);
        $data['payment_status'] = $xendit['status'];
        $data['payment_url'] = $xendit['invoice_url'];
        $enrolment = Enrolment::create($data);

        $enrolment['subject'] = "Enrolment Payment of $enrolment->child_name - Mutiara Harapan Islamic School";
        $enrolment['template'] = 'email-template.enrolment';
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
}
