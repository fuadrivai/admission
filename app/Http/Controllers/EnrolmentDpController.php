<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEnrolmentDpRequest;
use App\Models\AcademicYear;
use App\Models\Branch;
use App\Models\EnrolmentTransaction;
use App\Models\RegistrationPlace;
use App\Services\BankChargerService;
use App\Services\EnrolmentDiscountService;
use App\Services\EnrolmentDpService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EnrolmentDpController extends Controller
{
    private $enrolmentDpService;
    private $bankChargerService;

    public function __construct(
        EnrolmentDpService $enrolmentDpService,
        BankChargerService $bankChargerService
    ) {
        $this->enrolmentDpService = $enrolmentDpService;
        $this->bankChargerService = $bankChargerService;
        $this->middleware(function ($request, $next) {
            abort_if(auth()->user()->role === 'user', 403);

            return $next($request);
        });
    }

    public function index(Request $request)
    {
        $transaction = null;
        if ($request->session()->has('dp_transaction_id')) {
            $transaction = EnrolmentTransaction::with(['enrolment', 'details'])
                ->find($request->session()->pull('dp_transaction_id'));
        }

        return view('enrolment.dp', [
            'title' => 'Development Fee',
            'branches' => Branch::where('show_in_form', true)->orderBy('name')->get(),
            'academicYears' => AcademicYear::orderByDesc('name')->get(),
            'registrationPlaces' => RegistrationPlace::where('is_active', true)->orderBy('id')->get(),
            'bankCharge' => (float) optional($this->bankChargerService->get())->price,
            'requestKey' => old('request_key', (string) Str::uuid()),
            'transaction' => $transaction,
            'prefillCode' => trim((string) $request->query('code', '')),
        ]);
    }

    public function search(Request $request)
    {
        $code = trim((string) $request->query('code'));
        if ($code === '') {
            return response()->json([
                'success' => false,
                'message' => 'Enter an enrolment code to search.',
            ], 422);
        }

        $result = $this->enrolmentDpService->searchEnrolment($code);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    public function discount(Request $request)
    {
        $amount = (float) $request->query('enrolment_amount', 0);
        $lines = [['type' => 'enrolment', 'amount' => $amount]];
        if ($request->boolean('has_dp')) {
            $lines[] = ['type' => 'dp', 'amount' => 1];
        }
        $result = (new EnrolmentDiscountService())->resolve($request->query('registration_place'), $lines, false, $request->query('branch_id'));

        return response()->json([
            'discount' => $result['amount'] ?? 0,
            'rule' => $result ? $result['rule']->name : null,
        ]);
    }

    public function store(StoreEnrolmentDpRequest $request)
    {
        $transaction = $this->enrolmentDpService->createTransaction($request->validated());

        return redirect()->route('enrolment.dp.index')
            ->with('dp_transaction_id', $transaction->id);
    }
}
