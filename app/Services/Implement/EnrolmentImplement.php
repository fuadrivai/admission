<?php

namespace App\Services\Implement;

use App\Mail\AdmissionEmail;
use App\Models\Branch;
use App\Models\EmailSetting;
use App\Models\Enrolment;
use App\Models\EnrolmentTransaction;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\ValidationException;

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
        return Enrolment::with('prospect.activities')->findOrFail($id);
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

        $requiredItems = $this->enrolmentPriceService->getRequiredPriceItems(
            $data['academic_year_id'],
            $data['branch_id'],
            $data['level_id'],
            $data['grade_id']
        );
        $this->ensureRequiredEnrolmentPriceItems($requiredItems);
        $enrolmentFee = $this->sumPriceItemsByType($requiredItems, 'enrolment');
        $parseValue = $this->calculateSeatAndForm($request, $enrolmentFee);
        $bank = $this->bankChargerService->get();
        $data['bank_charger'] = (float) optional($bank)->price;
        $data['registration_fee'] = $enrolmentFee - $parseValue['registration_discount'];
        $data['custom_payment'] = $parseValue['seat_rsvp'] > 0 ? $parseValue['seat_rsvp'] : $parseValue['full_payment'];
        $data['discount'] = $parseValue['registration_discount'];
        $data['amount_paid'] = $this->requiredPriceTotal($requiredItems)
            + $data['custom_payment'] - $data['discount'] + $data['bank_charger'];
        $data['noted'] = "Enrolment created with custom form input: " . $request->option
            . " resulting in custom form value: " . $data['custom_payment']
            . " academic year: " . $request->academicYear
            . " with discount applied: " . $parseValue['registration_discount'];

        $level = Level::findOrFail($data['level_id']);
        $grade = Grade::findOrFail($data['grade_id']);
        $level_name = $level->name;
        $grade_name = $grade->name;
        $enrolment = DB::transaction(function () use ($data, $request, $requiredItems, $parseValue, $level) {
            if ($data['already_visit'] == 0) {
                $data['code'] = generate($level->branch_code, ['prospects', 'enrolments']);
                $prospects = $this->saveProspects($data);
                $data['prospects_id'] = $prospects->id;
            } else {
                $data['code'] = $request->code;
                if (empty($request->prospectsId)) {
                    $prospects = $this->saveProspects($data);
                    $data['prospects_id'] = $prospects->id;
                } else {
                    $data['prospects_id'] = $request->prospectsId;
                }
            }

            $enrolment = Enrolment::create($data);
            $extraItems = [];
            if ($data['custom_payment'] > 0) {
                $extraItems[] = [
                    'type' => $parseValue['full_payment'] > 0 ? 'fullpayment' : 'other',
                    'description' => $parseValue['full_payment'] > 0 ? 'Full Payment' : 'Seat Reservation',
                    'amount' => (float) $data['custom_payment'],
                ];
            }
            $this->createInitialPaymentTransaction(
                $enrolment,
                $requiredItems,
                $extraItems,
                (float) $data['discount'],
                (float) $data['bank_charger'],
                $level->name
            );
            $enrolment->activities()->create([
                'prospects_id' => $enrolment->prospects_id,
                'note' => "Enrolment created with invoice ID " . $enrolment->invoice_id
                    . " and payment status: " . $enrolment->payment_status,
            ]);

            return $enrolment;
        });

        $this->attachPaymentSummary($enrolment);
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
            'already_visit'            => ($request->alreadyVisit ?? null) === 'true'?1:0,
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
            'child_nick_name'          => $request->childNickname,
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

        $requiredItems = $this->enrolmentPriceService->getRequiredPriceItems(
            $data['academic_year_id'],
            $data['branch_id'],
            $data['level_id'],
            $data['grade_id']
        );
        $this->ensureRequiredEnrolmentPriceItems($requiredItems);
        $bank = $this->bankChargerService->get();
        $data['bank_charger'] = (float) optional($bank)->price;
        $data['registration_fee'] = $this->sumPriceItemsByType($requiredItems, 'enrolment');
        $data['discount'] = 0;
        $data['amount_paid'] = $this->requiredPriceTotal($requiredItems) + $data['bank_charger'];
        $level = Level::findOrFail($data['level_id']);
        $grade = Grade::findOrFail($data['grade_id']);
        $level_name = $level->name;
        $grade_name = $grade->name;

        $enrolment = DB::transaction(function () use ($data, $request, $requiredItems, $level) {
            if ($data['already_visit'] == 0) {
                $data['code'] = generate($level->branch_code, ['prospects', 'enrolments']);
                $prospects = $this->saveProspects($data);
                $data['prospects_id'] = $prospects->id;
            } else {
                $data['code'] = $request->code;
                if (empty($request->prospectsId)) {
                    $prospects = $this->saveProspects($data);
                    $data['prospects_id'] = $prospects->id;
                } else {
                    $data['prospects_id'] = $request->prospectsId;
                }
            }

            $enrolment = Enrolment::create($data);
            $this->createInitialPaymentTransaction(
                $enrolment,
                $requiredItems,
                [],
                (float) $data['discount'],
                (float) $data['bank_charger'],
                $level->name
            );
            $enrolment->activities()->create([
                'prospects_id' => $enrolment->prospects_id,
                'note' => "Enrolment created with invoice ID " . $enrolment->invoice_id
                    . " and payment status: " . $enrolment->payment_status,
            ]);

            return $enrolment;
        });

        $this->attachPaymentSummary($enrolment);
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

    private function ensureRequiredEnrolmentPriceItems($items): void
    {
        if ($items->isEmpty() || !$items->contains(function ($item) {
            return $item->type === 'enrolment';
        })) {
            throw ValidationException::withMessages([
                'payment' => 'No active required Enrolment Fee price item is configured for the selected academic year, branch, level, and grade.',
            ]);
        }
    }

    private function sumPriceItemsByType($items, string $type): float
    {
        return (float) $items->where('type', $type)->sum(function ($item) {
            return (float) $item->amount;
        });
    }

    private function requiredPriceTotal($items): float
    {
        return (float) $items->sum(function ($item) {
            return (float) $item->amount;
        });
    }

    private function attachPaymentSummary(Enrolment $enrolment): void
    {
        $transaction = $enrolment->transactions()
            ->with('details')
            ->where('invoice_id', $enrolment->invoice_id)
            ->firstOrFail();

        $enrolment['payment_details'] = $transaction->details
            ->map(function ($detail) {
                return [
                    'description' => $detail->description,
                    'amount' => (float) $detail->amount,
                ];
            })
            ->all();
        $enrolment['payment_discount'] = (float) $transaction->discount;
        $enrolment['bank_charger'] = (float) $transaction->bank_charge;
        $enrolment['amount_paid'] = (float) $transaction->total_amount;
    }

    private function createInitialPaymentTransaction(
        Enrolment $enrolment,
        $requiredItems,
        array $additionalItems,
        float $discount,
        float $bankCharge,
        string $levelName
    ): EnrolmentTransaction {
        $enrolmentFee = $this->sumPriceItemsByType($requiredItems, 'enrolment');
        if ($discount < 0 || $discount > $enrolmentFee) {
            throw ValidationException::withMessages([
                'discount' => 'Discount cannot exceed the Enrolment Fee component.',
            ]);
        }

        $details = $requiredItems->map(function ($item) {
            return [
                'type' => $item->type,
                'description' => $item->name,
                'amount' => (float) $item->amount,
                'is_required_charge' => true,
            ];
        })->all();
        foreach ($additionalItems as $item) {
            $item['is_required_charge'] = false;
            $details[] = $item;
        }

        $subtotal = round(array_sum(array_column($details, 'amount')) - $discount, 2);
        $total = round($subtotal + $bankCharge, 2);
        if ($total <= 0) {
            throw ValidationException::withMessages([
                'payment' => 'The initial enrolment payment total must be greater than zero.',
            ]);
        }

        $invoiceId = $this->generateInvoiceId();
        $branch = Branch::findOrFail($enrolment->branch_id);
        $gradeName = optional($enrolment->grade)->name;
        $xendit = createXenditInvoice([
            'external_id' => $invoiceId,
            'amount' => $total,
            'payer_email' => $enrolment->email,
            'description' => 'Enrolment payment - ' . $enrolment->child_name
                . ' for ' . $enrolment->academic_year . ' - ' . $levelName . ' ' . $gradeName,
            'invoice_duration' => 60 * 60 * 24 * 7,
        ], $branch->name ?? 'bintaro');

        if (isset($xendit['success']) && $xendit['success'] === false) {
            throw ValidationException::withMessages([
                'payment' => 'Could not create Xendit invoice: ' . ($xendit['message'] ?? 'Unknown Xendit error.'),
            ]);
        }
        if (empty($xendit['status']) || empty($xendit['invoice_url'])
            || empty($xendit['created']) || empty($xendit['expiry_date'])) {
            throw ValidationException::withMessages([
                'payment' => 'Xendit returned an incomplete invoice response. The enrolment payment was not saved.',
            ]);
        }

        $paymentFields = [
            'invoice_id' => $invoiceId,
            'payment_status' => strtoupper($xendit['status']),
            'payment_url' => $xendit['invoice_url'],
            'create_va_date' => Carbon::parse($xendit['created']),
            'expiry_va_date' => Carbon::parse($xendit['expiry_date']),
            'registration_fee' => $enrolmentFee - $discount,
            'bank_charger' => $bankCharge,
            'discount' => $discount,
            'amount_paid' => $total,
        ];
        $enrolment->update($paymentFields);

        $transaction = $enrolment->transactions()->create([
            'code' => $enrolment->code,
            'invoice_id' => $invoiceId,
            'subtotal' => $subtotal + $discount,
            'discount' => $discount,
            'bank_charge' => $bankCharge,
            'total_amount' => $total,
            'payment_status' => strtoupper($xendit['status']),
            'create_va_date' => Carbon::parse($xendit['created']),
            'expiry_va_date' => Carbon::parse($xendit['expiry_date']),
            'payment_url' => $xendit['invoice_url'],
            'payment_place' => $enrolment->regis_place,
            'source' => $enrolment->source_data,
            'noted' => $enrolment->noted,
            'created_by' => auth()->id(),
        ]);

        $discountRemaining = $discount;
        foreach ($details as $detail) {
            $lineDiscount = $detail['type'] === 'enrolment'
                ? min($discountRemaining, (float) $detail['amount'])
                : 0;
            $discountRemaining -= $lineDiscount;
            $transaction->details()->create([
                'type' => $detail['type'],
                'description' => $detail['description'],
                'amount' => $detail['amount'],
                'discount' => $lineDiscount,
                'subtotal' => round((float) $detail['amount'] - $lineDiscount, 2),
                'is_required_charge' => $detail['is_required_charge'],
            ]);
        }

        return $transaction;
    }

    private function generateInvoiceId(): string
    {
        $prefix = env('PREFIX_XENDIT') ?: 'INV-ENROL';
        $date = now()->format('ymd');
        $pattern = $prefix . $date . '%';
        $invoiceIds = DB::table('enrolments')
            ->where('invoice_id', 'like', $pattern)
            ->lockForUpdate()
            ->pluck('invoice_id')
            ->merge(
                DB::table('enrolment_transactions')
                    ->where('invoice_id', 'like', $pattern)
                    ->lockForUpdate()
                    ->pluck('invoice_id')
            );
        $lastSequence = $invoiceIds->map(function ($invoiceId) use ($prefix, $date) {
            $sequence = substr($invoiceId, strlen($prefix) + strlen($date));

            return ctype_digit($sequence) ? (int) $sequence : 0;
        })->max() ?: 0;

        return $prefix . $date . str_pad((string) ($lastSequence + 1), 4, '0', STR_PAD_LEFT);
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

            // $registration = $registration_form ?? 0;
            // $fullPayment = $amount - $registration;
            $registration = 0;
            $seat = $amount;
            $fullPayment = $amount;
        }

        return [
            'registration_form' => $registration,
            'seat_rsvp' => $seat,
            'full_payment' => $fullPayment,
        ];
    }

    private function applyRegistrationDiscount($registration, $place,$level)
    {
        $discount = 0;
        $discountAmount = 0;

        if ($registration <= 0) {
            return [
                'original' => 0,
                'discount' => 0,
                'final' => 0,
            ];
        }

        if ($place === 'Openday') {
            $discount = 0;
        }

        $discountAmount = $registration * $discount;

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

    public function search($request)
    {
        $query = Enrolment::query()->with('transactions.details');
        if (auth()->check() && auth()->user()->role == 'user') {
            $query->where('branch_id', auth()->user()->branch_id);
        }
        $query->orderBy('created_at', 'desc');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('code', 'like', '%'.$request->search.'%')
                ->orWhere('parent_name', 'like', '%'.$request->search.'%')
                ->orWhere('email', 'like', '%'.$request->search.'%')
                ->orWhere('child_name', 'like', '%'.$request->search.'%')
                ->orWhere('invoice_id', 'like', '%'.$request->search.'%')
                ->orWhere('phone_number', 'like', '%'.$request->search.'%');
                });
        }

        if ($request->level && $request->level !== 'all') {
            $query->where('level_id', $request->level);
        }
        if ($request->branch && $request->branch !== 'all') {
            $query->where('branch_id', $request->branch);
        }
        if ($request->grade && $request->grade !== 'all') {
            $query->where('grade_id', $request->grade);
        }
        if ($request->status && $request->status !== 'all') {
            $query->where('payment_status', $request->status);
        }

        if ($request->source_data && $request->source_data !== 'all') {
            $query->where('source_data', $request->source_data);
        }

        if ($request->data_from && $request->data_from !== 'all') {
            $query->where('data_from', $request->data_from);
        }

        if ($request->regis_place && $request->regis_place !== 'all') {
            $query->where('regis_place', $request->regis_place);
        }
        if ($request->academic_year && $request->academic_year !== 'all') {
            $query->where('academic_year_id', $request->academic_year);
        }

        if ($request->start_date && $request->start_date != '') {
            $startDate = Carbon::createFromFormat('d F Y', $request->start_date)->format('Y-m-d');
            $endate = null;
            if ($request->end_date && $request->end_date != '') {
                $endate = Carbon::createFromFormat('d F Y', $request->end_date)->format('Y-m-d');
                $query->whereBetween('created_at', [$startDate ." 00:00:00", $endate ." 23:59:59"]);
            }else{
                $query->whereBetween('created_at', [$startDate ." 00:00:00", $startDate ." 23:59:59"]);
            }
        }
        return $query;
    }

    public function summary($query)
    {
        $statusQuery = clone $query;

        $statusCounts = $statusQuery
            ->reorder()
            ->selectRaw("payment_status, COUNT(*) as total")
            ->groupBy('payment_status')
            ->pluck('total', 'payment_status');

        $totalFiltered = (clone $query)->reorder()->count();

        $visitCount = (clone $query)->whereHas('prospect.schoolVisit')->count();

        $summary = [
            'pending' => $statusCounts['PENDING'] ?? 0,
            'paid' => $statusCounts['PAID'] ?? 0,
            'expired' => $statusCounts['EXPIRED'] ?? 0,
            'cancelled' => $statusCounts['CANCELLED'] ?? 0,
            'total' => $totalFiltered,
            'visitSummary' => [
                'visit' => $visitCount,
                'registered' => (clone $query)->whereHas('prospect.schoolVisit', function($q) {
                    $q->where('status', 'registered');
                })->count(),
                'present' => (clone $query)->whereHas('prospect.schoolVisit', function($q) {
                    $q->where('status', 'present');
                })->count(),
                'cancelled' => (clone $query)->whereHas('prospect.schoolVisit', function($q) {
                    $q->where('status', 'cancelled');
                })->count(),
                'absent' => (clone $query)->whereHas('prospect.schoolVisit', function($q) {
                    $q->where('status', 'absent');
                })->count(),
            ],
        ];
        return $summary;
    }
}
