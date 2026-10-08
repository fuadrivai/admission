<?php

namespace App\Services\Implement;

use App\Models\RegistrationPlace;
use App\Models\AcademicYear;
use App\Models\Branch;
use App\Models\Enrolment;
use App\Models\EnrolmentTransaction;
use App\Models\Grade;
use App\Models\Level;
use App\Models\Prospects;
use App\Services\BankChargerService;
use App\Services\EnrolmentDpService;
use App\Services\EnrolmentPriceService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

use function App\Helpers\createXenditInvoice;
use function App\Helpers\generate;
use function App\Helpers\normalizePhoneNumber;

class EnrolmentDpImplement implements EnrolmentDpService
{
    private $enrolmentPriceService;
    private $bankChargerService;

    public function __construct(
        EnrolmentPriceService $enrolmentPriceService,
        BankChargerService $bankChargerService
    ) {
        $this->enrolmentPriceService = $enrolmentPriceService;
        $this->bankChargerService = $bankChargerService;
    }

    public function searchEnrolment(string $code): array
    {
        $enrolment = Enrolment::with(['branch', 'level', 'grade', 'year'])
            ->where('code', trim($code))
            ->first();

        if (!$enrolment) {
            return [
                'success' => false,
                'status' => 'NOT_FOUND',
                'message' => 'Enrolment code not found.',
            ];
        }

        $paymentStatus = strtoupper((string) $enrolment->payment_status);
        if ($paymentStatus === 'EXPIRED') {
            return [
                'success' => false,
                'status' => 'EXPIRED',
                'message' => 'This enrolment has expired. A new enrolment will be created using the existing student and parent details.',
                'data' => $this->expiredEnrolmentData($enrolment),
            ];
        }

        if ($paymentStatus === 'PENDING') {
            return [
                'success' => false,
                'status' => 'PENDING',
                'message' => 'This enrolment payment is still pending. Complete the existing payment before creating another enrolment.',
            ];
        }

        try {
            $this->validateExistingEnrolment($enrolment);
        } catch (ValidationException $exception) {
            return [
                'success' => false,
                'status' => $paymentStatus ?: 'UNPAID',
                'message' => "This enrolment has not been paid yet (status: " . ($paymentStatus ?: 'UNPAID') . '). Please complete the existing enrolment payment first.',
            ];
        }

        $requiredItems = $this->enrolmentPriceService->getRequiredPriceItems(
            $enrolment->academic_year_id,
            $enrolment->branch_id,
            $enrolment->level_id,
            $enrolment->grade_id
        );
        $paidTypes = $this->paidRequiredTypes($enrolment, $requiredItems);
        $requiredItems = $requiredItems->map(function ($item) use ($paidTypes) {
            $item->setAttribute('is_paid', in_array($item->type, $paidTypes, true));

            return $item;
        });

        return [
            'success' => true,
            'data' => [
                'id' => $enrolment->id,
                'code' => $enrolment->code,
                'student_name' => $enrolment->child_name,
                'parent_name' => $enrolment->parent_name,
                'email' => $enrolment->email,
                'phone' => $enrolment->phone_number,
                'branch_id' => $enrolment->branch_id,
                'level_id' => $enrolment->level_id,
                'grade_id' => $enrolment->grade_id,
                'academic_year_id' => $enrolment->academic_year_id,
                'academic_year' => $enrolment->academic_year,
                'branch' => optional($enrolment->branch)->name,
                'level' => optional($enrolment->level)->name,
                'grade' => optional($enrolment->grade)->name,
                'payment_status' => strtoupper($enrolment->payment_status),
                'registration_fee' => (float) $requiredItems
                    ->where('type', 'enrolment')
                    ->sum(function ($item) {
                        return (float) $item->amount;
                    }),
                'required_price_items' => $requiredItems->values(),
            ],
        ];
    }

    private function expiredEnrolmentData(Enrolment $enrolment): array
    {
        return [
            'code' => $enrolment->code,
            'student_name' => $enrolment->child_name,
            'parent_name' => $enrolment->parent_name,
            'email' => $enrolment->email,
            'phone' => $enrolment->phone_number,
            'academic_year_id' => $enrolment->academic_year_id,
            'branch_id' => $enrolment->branch_id,
            'level_id' => $enrolment->level_id,
            'grade_id' => $enrolment->grade_id,
        ];
    }

    public function validateExistingEnrolment(Enrolment $enrolment): void
    {
        if (!$enrolment->hasPaidRegistrationPayment()) {
            $paymentStatus = strtoupper((string) $enrolment->payment_status);
            $message = $paymentStatus === 'EXPIRED'
                ? 'This enrolment has expired. Select "No, new parent" to create a new enrolment.'
                : ($paymentStatus === 'PENDING'
                    ? 'This enrolment payment is still pending. Complete the existing payment before creating another enrolment.'
                    : 'This enrolment has not completed its registration payment. Please use the existing enrolment payment process first.');

            throw ValidationException::withMessages([
                'enrolment_code' => $message,
            ]);
        }
    }

    public function getRegistrationFee($academicYearId, $branchId, $levelId, $gradeId)
    {
        if ($academicYearId) {
            AcademicYear::findOrFail($academicYearId);
        }
        $branch = Branch::findOrFail($branchId);
        $level = Level::findOrFail($levelId);
        $grade = Grade::findOrFail($gradeId);

        if ((int) $level->branch_id !== (int) $branch->id || (int) $grade->level_id !== (int) $level->id) {
            throw ValidationException::withMessages([
                'payment_type' => 'The selected branch, level, and grade do not match.',
            ]);
        }

        return $this->enrolmentPriceService->getRegistrationPrice(
            $branchId,
            $levelId,
            $academicYearId,
            $gradeId
        );
    }

    public function createTransaction(array $data): EnrolmentTransaction
    {
        $requestKey = $data['request_key'];

        try {
            return DB::transaction(function () use ($data, $requestKey) {
                $existingTransaction = EnrolmentTransaction::with(['enrolment', 'details'])
                    ->where('request_key', $requestKey)
                    ->first();

                if ($existingTransaction) {
                    return $existingTransaction;
                }

                if ($data['already_enrolment'] === 'yes') {
                    $enrolment = Enrolment::where('code', trim($data['enrolment_code']))
                        ->lockForUpdate()
                        ->firstOrFail();
                    $this->validateExistingEnrolment($enrolment);
                } else {
                    $academicYear = AcademicYear::findOrFail($data['academic_year_id']);
                    $branch = Branch::findOrFail($data['branch_id']);
                    $level = Level::findOrFail($data['level_id']);
                    $grade = Grade::findOrFail($data['grade_id']);

                    if ((int) $level->branch_id !== (int) $branch->id || (int) $grade->level_id !== (int) $level->id) {
                        throw ValidationException::withMessages([
                            'grade_id' => 'The selected branch, level, and grade do not match.',
                        ]);
                    }

                    $enrolmentCode = generate($level->branch_code, ['prospects', 'enrolments']);
                    $prospect = Prospects::create([
                        'code' => $enrolmentCode,
                        'child_name' => $data['student_name'],
                        'date_of_birth' => null,
                        'place_of_birth' => null,
                        'current_school' => null,
                        'parent_name' => $data['parent_name'],
                        'email' => $data['email'],
                        'phone_number' => normalizePhoneNumber($data['phone_number']),
                        'zipcode' => null,
                        'address' => null,
                        'relationship' => null,
                        'source_module' => 'enrolment',
                    ]);

                    $enrolment = Enrolment::create([
                        'prospects_id' => $prospect->id,
                        'already_visit' => false,
                        'code' => $enrolmentCode,
                        'is_current_student' => 'no',
                        'branch_id' => $branch->id,
                        'level_id' => $level->id,
                        'grade_id' => $grade->id,
                        'academic_year_id' => $academicYear->id,
                        'academic_year' => $academicYear->name,
                        'parent_name' => $data['parent_name'],
                        'email' => $data['email'],
                        'phone_number' => normalizePhoneNumber($data['phone_number']),
                        'relationship' => null,
                        'zipcode' => null,
                        'address' => null,
                        'child_name' => $data['student_name'],
                        'date_of_birth' => null,
                        'place_of_birth' => null,
                        'current_school' => null,
                        'open_day_visited' => false,
                        'knowledge_about_program' => null,
                        'info_from' => null,
                        'reason_for_enrolment' => null,
                        'preferred_program' => null,
                        'expectation_mhis_impact' => null,
                        'registration_fee' => 0,
                        'custom_payment' => 0,
                        'bank_charger' => 0,
                        'discount' => 0,
                        'amount_paid' => 0,
                        'invoice_id' => null,
                        'payment_status' => 'unpaid',
                        'source_data' => $data['registration_type'],
                        'regis_place' => $this->registrationPlace($data),
                        'data_from' => 'custom_form',
                    ]);
                }

                $paymentTypes = array_values(array_unique($data['payment_types'] ?? []));
                if ($data['already_enrolment'] === 'yes' && in_array('dp', $paymentTypes, true) && $enrolment->hasActiveDpPayment()) {
                    throw ValidationException::withMessages([
                        'payment_types' => 'A Development Fee DP is already paid or still pending for this enrolment.',
                    ]);
                }
                if ($data['already_enrolment'] === 'yes' && in_array('enrolment', $paymentTypes, true)) {
                    throw ValidationException::withMessages([
                        'payment_types' => 'Registration Fee cannot be charged again to an existing enrolment.',
                    ]);
                }

                $requiredItems = $this->enrolmentPriceService->getRequiredPriceItems(
                    $enrolment->academic_year_id,
                    $enrolment->branch_id,
                    $enrolment->level_id,
                    $enrolment->grade_id
                );
                if ($requiredItems->isEmpty() || !$requiredItems->contains(function ($item) {
                    return $item->type === 'enrolment';
                })) {
                    throw ValidationException::withMessages([
                        'payment_types' => 'No active required enrolment price items are configured for this selection.',
                    ]);
                }

                $paidTypes = $data['already_enrolment'] === 'yes'
                    ? $this->paidRequiredTypes($enrolment, $requiredItems)
                    : [];
                $lineItems = [];
                $chargeRequired = $data['already_enrolment'] === 'yes' || in_array('enrolment', $paymentTypes, true);
                foreach ($requiredItems as $requiredItem) {
                    if ($chargeRequired && !in_array($requiredItem->type, $paidTypes, true)) {
                        $lineItems[] = [
                            'type' => $requiredItem->type,
                            'description' => $requiredItem->name,
                            'amount' => (float) $requiredItem->amount,
                            'is_required_charge' => true,
                        ];
                    }
                }

                foreach ($paymentTypes as $type) {
                    if ($type === 'enrolment') {
                        continue;
                    }
                    $lineItems[] = [
                        'type' => $type,
                        'description' => $this->paymentDescription($type, $data),
                        'amount' => $this->paymentAmount($type, $data, $enrolment),
                        'is_required_charge' => false,
                    ];
                }

                if (empty($lineItems)) {
                    throw ValidationException::withMessages([
                        'payment_types' => 'Select a DP or Other payment, or choose an enrolment with outstanding required charges.',
                    ]);
                }

                $amount = array_sum(array_column($lineItems, 'amount'));
                $resolvedDiscount = (new \App\Services\EnrolmentDiscountService())->resolve(
                    $data['registration_place'] ?? null,
                    $lineItems,
                    true,
                    $enrolment->branch_id
                );
                $discount = $resolvedDiscount['amount'] ?? 0.0;
                foreach ($lineItems as $i => $line) {
                    $lineItems[$i]['discount'] = ($resolvedDiscount && $resolvedDiscount['index'] === $i) ? $discount : 0.0;
                }
                $bankCharge = (float) optional($this->bankChargerService->get())->price;

                if ($discount > $amount) {
                    throw ValidationException::withMessages([
                        'discount' => 'Discount cannot exceed the payment amount.',
                    ]);
                }

                $subtotal = round($amount, 2);
                $total = round($subtotal - $discount + $bankCharge, 2);
                if ($total > 9999999999.99) {
                    throw ValidationException::withMessages([
                        'amount' => 'The total payment exceeds the supported amount.',
                    ]);
                }

                $branch = Branch::findOrFail($enrolment->branch_id);
                $invoiceId = $this->generateInvoiceId();
                $payload = [
                    'external_id' => $invoiceId,
                    'amount' => $total,
                    'payer_email' => $enrolment->email,
                    'description' => 'Enrolment payment - ' . $enrolment->child_name
                        . ' (' . $enrolment->code . ')',
                    'invoice_duration' => 60 * 60 * 24 * 7,
                ];
                $xendit = createXenditInvoice($payload, $branch->name ?? 'bintaro');
                if (!empty($xendit['success']) && $xendit['success'] === false) {
                    throw ValidationException::withMessages([
                        'payment' => 'Could not create Xendit invoice: ' . ($xendit['message'] ?? 'Unknown Xendit error.'),
                    ]);
                }
                if (empty($xendit['status']) || empty($xendit['invoice_url'])
                    || empty($xendit['created']) || empty($xendit['expiry_date'])) {
                    throw ValidationException::withMessages([
                        'payment' => 'Xendit returned an incomplete invoice response. No payment transaction was saved.',
                    ]);
                }

                $transaction = $enrolment->transactions()->create([
                    'code' => $enrolment->code,
                    'invoice_id' => $invoiceId,
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'discount_rule_id' => $resolvedDiscount['rule']->id ?? null,
                    'bank_charge' => $bankCharge,
                    'total_amount' => $total,
                    'payment_status' => strtoupper($xendit['status']),
                    'create_va_date' => Carbon::parse($xendit['created']),
                    'expiry_va_date' => Carbon::parse($xendit['expiry_date']),
                    'payment_url' => $xendit['invoice_url'],
                    'payment_place' => $this->registrationPlace($data),
                    'source' => $data['registration_type'],
                    'noted' => 'Payment transaction created through Development Fee form.',
                    'created_by' => auth()->id(),
                    'request_key' => $requestKey,
                ]);
                foreach ($lineItems as $lineItem) {
                    $lineDiscount = (float) $lineItem['discount'];
                    $transaction->details()->create([
                        'type' => $lineItem['type'],
                        'description' => $lineItem['description'],
                        'amount' => $lineItem['amount'],
                        'discount' => $lineDiscount,
                        'subtotal' => round($lineItem['amount'] - $lineDiscount, 2),
                        'is_required_charge' => $lineItem['is_required_charge'],
                    ]);
                }

                if ($data['already_enrolment'] !== 'yes') {
                    $enrolment->update([
                        'registration_fee' => collect($lineItems)->where('is_required_charge', true)->sum('amount'),
                        'custom_payment' => collect($lineItems)->whereIn('type', ['dp', 'other'])->sum('amount'),
                        'bank_charger' => $bankCharge,
                        'discount' => $discount,
                        'amount_paid' => $total,
                        'invoice_id' => $invoiceId,
                        'payment_status' => strtoupper($xendit['status']),
                        'create_va_date' => Carbon::parse($xendit['created']),
                        'expiry_va_date' => Carbon::parse($xendit['expiry_date']),
                        'payment_url' => $xendit['invoice_url'],
                    ]);
                }

                if ($enrolment->prospects_id) {
                    $items = collect($lineItems)->pluck('description')->implode(', ');
                    $enrolment->activities()->create([
                        'prospects_id' => $enrolment->prospects_id,
                        'note' => 'Development Fee form payment created with invoice ID ' . $invoiceId
                            . ' ('. $items . '), total Rp ' . number_format($total, 0, ',', '.')
                            . ', payment status: ' . $transaction->payment_status,
                    ]);
                }

                return $transaction->load(['enrolment', 'details']);
            });
        } catch (QueryException $exception) {
            $existingTransaction = EnrolmentTransaction::with(['enrolment', 'details'])
                ->where('request_key', $requestKey)
                ->first();

            if ($existingTransaction) {
                return $existingTransaction;
            }

            throw $exception;
        }
    }

    private function generateInvoiceId(): string
    {
        $prefix = env('PREFIX_XENDIT') ?: 'INV-ENROL';
        $date = now()->format('ymd');
        $pattern = $prefix . $date . '%';
        $invoiceIds = DB::table('enrolments')
            ->where('invoice_id', 'like', $pattern)
            ->orderByDesc('invoice_id')
            ->lockForUpdate()
            ->pluck('invoice_id')
            ->merge(
                DB::table('enrolment_transactions')
                    ->where('invoice_id', 'like', $pattern)
                    ->orderByDesc('invoice_id')
                    ->lockForUpdate()
                    ->pluck('invoice_id')
            );
        $lastSequence = $invoiceIds
            ->map(function ($invoiceId) use ($prefix, $date) {
                $sequence = substr($invoiceId, strlen($prefix) + strlen($date));

                return ctype_digit($sequence) ? (int) $sequence : 0;
            })
            ->max() ?: 0;

        return $prefix . $date . str_pad((string) ($lastSequence + 1), 4, '0', STR_PAD_LEFT);
    }

    private function paidRequiredTypes(Enrolment $enrolment, $requiredItems): array
    {
        $hasTransactionDetails = $enrolment->transactions()->whereHas('details')->exists();
        if (!$hasTransactionDetails
            && in_array(strtoupper((string) $enrolment->payment_status), ['PAID', 'SETTLED', 'COMPLETED'], true)) {
            return $requiredItems->pluck('type')->unique()->values()->all();
        }

        return $enrolment->transactions()
            ->whereRaw('UPPER(payment_status) IN (?, ?, ?)', ['PAID', 'SETTLED', 'COMPLETED'])
            ->with('details')
            ->get()
            ->flatMap(function ($transaction) {
                return $transaction->details
                    ->filter(function ($detail) {
                        return $detail->is_required_charge
                            || in_array($detail->type, ['enrolment', 'streaming_test'], true);
                    })
                    ->pluck('type');
            })
            ->unique()
            ->values()
            ->all();
    }

    private function paymentAmount(string $type, array $data, Enrolment $enrolment): float
    {
        if ($type === 'enrolment') {
            $price = $this->getRegistrationFee(
                $enrolment->academic_year_id,
                $enrolment->branch_id,
                $enrolment->level_id,
                $enrolment->grade_id
            );

            if (!$price) {
                throw ValidationException::withMessages([
                    'payment_type' => 'No active registration fee is configured for this branch and level.',
                ]);
            }

            return (float) $price->price;
        }

        return round((float) $data['amounts'][$type], 2);
    }

    private function paymentDescription(string $type, array $data): string
    {
        if ($type === 'enrolment') {
            return 'Registration Fee';
        }
        if ($type === 'dp') {
            return 'Development Fee DP';
        }

        return trim($data['descriptions']['other'] ?? '') ?: 'Other payment';
    }

    private function registrationPlace(array $data): string
    {
        if (RegistrationPlace::where('code', $data['registration_place'])->where('is_other', true)->exists()) {
            return trim($data['registration_place_other']);
        }

        return $data['registration_place'];
    }
}
