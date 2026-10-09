<?php

namespace App\Http\Controllers;

use App\Exports\EnrolmentExport;
use App\Models\Enrolment;
use App\Models\EnrolmentTransaction;
use App\Services\BranchService;
use App\Services\EnrolmentService;
use App\Services\ProspectService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Yajra\DataTables\Utilities\Request as UtilitiesRequest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Maatwebsite\Excel\Facades\Excel;
use function App\Helpers\expireXenditInvoice;

class EnrolmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private BranchService $branchService;
    private EnrolmentService $enrolmentService;
    private ProspectService $prospectService;
    public function __construct(BranchService $branchService, EnrolmentService $enrolmentService, ProspectService $prospectService)
    {
        $this->branchService = $branchService;
        $this->enrolmentService = $enrolmentService;
        $this->prospectService = $prospectService;
    }
    public function index(Request $request)
    {
        $query = $this->enrolmentService->search($request);
        $summary = $this->enrolmentService->summary($query);
        $enrolments = $query->paginate(request('perpage')??10)->withQueryString();
        if ($request->ajax()) {
            return view('enrolment._list', compact('enrolments', 'summary'))->render();
        }

        return view('enrolment.index', ["title" => "Enrolment", "enrolments" => $enrolments, "summary" => $summary]);
    }

   public function datatables(UtilitiesRequest $request)
    {

        $enrolment = Enrolment::query();
        if ($request->ajax()) {
            return datatables()->of($enrolment->with(['branch','grade', 'level']))
                ->addColumn('branch_name', function ($row) {
                    return $row->branch ? $row->branch->name : '-';
                })
                ->addColumn('level_name', function ($row) {
                    return $row->level ? $row->level->name : '-';
                })
                ->addColumn('grade_name', function ($row) {
                    return $row->grade ? $row->grade->name : '-';
                })
                ->make(true);
        }

        return view('enrolment.index', ["title" => "Enrolment"]);
    }
    public function setting()
    {
        $branches = $this->branchService->getShowInForm();

        return view('enrolment.setting', compact('branches'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Enrolment  $enrolment
     * @return \Illuminate\Http\Response
     */
    public function showByCode($code)
    {
        try {
            $enrolment = $this->enrolmentService->showByCode($code,['branch', 'grade', 'level','prospect']);
            return response()->json($enrolment);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Enrollment code not found'
            ], $e->getCode() ?: 404);
        }
    }

    public function show(Enrolment $enrolment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Enrolment  $enrolment
     * @return \Illuminate\Http\Response
     */
    public function edit(Enrolment $enrolment)
    {
        $enrolment = $this->enrolmentService->show($enrolment->id)->load('transactions.details');
        return view('enrolment.detail', ["title" => "Enrolment Detail", "enrolment" => $enrolment]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Enrolment  $enrolment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Enrolment $enrolment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Enrolment  $enrolment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Enrolment $enrolment)
    {
        $expireResponse = $this->expirePendingInvoice($enrolment);
        if ($expireResponse !== null) {
            return $expireResponse;
        }

        $enrolment->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Enrolment deleted successfully.',
        ]);
    }
    

    public function form()
    {
        $branches = $this->branchService->getShowInForm();
        return view('enrolment.form.external', compact('branches'));
    }

    public function post(Request $request)
    {
        
        $rules = [
            'alreadyVisit'          => 'required',
            'code'                  => 'nullable|string',
            'prospectsId'           => 'nullable|string',
            'isCurrentStudent'      =>'required',
            'studentBranch'         => 'nullable|string',
            'mhisPortalUsername'    => 'nullable|string',
            'branch'                => 'required',
            'level'                 => 'required',
            'grade'                 => 'required',
            'academicYearId'        => 'required|integer',
            'academicYear'          => 'required|string',
            'parentName'            => 'required|string|max:255',
            'email'                 => 'required',
            'phone'                 => 'required|string|max:20',
            'relationship'          => 'required|string',
            'zipCode'               => 'nullable|string|max:20',
            'address'               => 'required|string',
            'childName'             => 'required|string|max:255',
            'childNickname'         => 'required|string|max:255',
            'placeOfBirth'          => 'required|string|max:120',
            'dateOfBirth'           => 'required',
            'currentSchool'         => 'nullable|string|max:255',
            'childSosmed'           => 'nullable|string|max:255',
            'opendayVisited'        => 'required',
            'knowledgeAboutProgram' => 'required|in:yes,no,maybe',
            'infoFrom'              => 'required|string',
            'infoFromMessage'       => 'nullable|string',
            'reasonForEnrolment'    => 'required|string',
            'prefferedProgram'      => 'required|string',
            'expectationMhisImpact' => 'required|string',
            'recommenderName'       => 'nullable|string|max:255',
            'recommenderPhone'      => 'nullable|string|max:20',
            'recommenderChildName'  => 'nullable|string|max:255',
            'recommenderChildClass' => 'nullable|string|max:120',
        ];

        if ($request->alreadyVisit === "true") {
            $rules['code'] = 'required|string';
        }

        $validated = $request->validate($rules);
        $enrolment = $this->enrolmentService->post((object)$validated);

        return response()->json([
            'status'    => 'success',
            'message'   => 'Enrolment form submitted successfully.',
            'data'      => $enrolment,
        ]);
    }

    public function export(Request $request)
    {
        $query = $this->enrolmentService->search($request);
        $timestamps = Carbon::now()->format('Ymd_His');
        return Excel::download(
            new EnrolmentExport($query->get()),
            'Enrolment_Report_' . $timestamps . '.xlsx',
            \Maatwebsite\Excel\Excel::XLSX
        );
    }

    public function history($id)
    {
        $prospect = $this->prospectService->show($id);
        return view('schoolvisit._history', compact('prospect'))->render();
    }

    public function cancel(Request $request, Enrolment $enrolment)
    {
        $validated = $request->validate([
            'reason' => 'required|string|max:1000',
        ]);

        if (strtoupper((string) $enrolment->payment_status) === 'CANCELLED') {
            return response()->json(['status' => 'error', 'message' => 'Enrolment is already cancelled.'], 422);
        }

        $expireResponse = $this->expirePendingInvoice($enrolment);
        if ($expireResponse !== null) {
            return $expireResponse;
        }

        EnrolmentTransaction::where('code', $enrolment->code)
            ->whereRaw('UPPER(payment_status) = ?', ['PENDING'])
            ->update(['payment_status' => 'EXPIRED']);

        $enrolment->payment_status = 'CANCELLED';
        $enrolment->cancel_reason = $validated['reason'];
        $enrolment->cancelled_at = now();
        $enrolment->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Enrolment cancelled successfully.',
        ]);
    }

    private function expirePendingInvoice(Enrolment $enrolment): ?\Illuminate\Http\JsonResponse
    {
        $branchName = $enrolment->branch->name ?? 'bintaro';

        $transactions = EnrolmentTransaction::where('code', $enrolment->code)
            ->whereRaw('UPPER(payment_status) = ?', ['PENDING'])
            ->get();

        if ($transactions->isNotEmpty()) {
            $paymentUrls = $transactions->pluck('payment_url')->filter()->unique();
        } elseif (strtoupper((string) $enrolment->payment_status) === 'PENDING') {
            // Legacy data: invoice is stored on the enrolment itself.
            $paymentUrls = collect([(string) $enrolment->payment_url]);
        } else {
            return null;
        }

        foreach ($paymentUrls as $paymentUrl) {
            $result = expireXenditInvoice((string) $paymentUrl, $branchName);

            if (!($result['success'] ?? false)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Xendit invoice could not be expired. The enrolment was not changed.',
                    'details' => $result['message'] ?? 'Unknown Xendit error.',
                ], 502);
            }
        }

        return null;
    }

    public function updateSourceData(Request $request, Enrolment $enrolment)
    {
        $validated = $request->validate([
            'source_data' => 'required|in:internal,external',
        ]);

        $enrolment->source_data = $validated['source_data'];
        $enrolment->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Source data updated successfully.',
            'data' => [
                'id' => $enrolment->id,
                'source_data' => $enrolment->source_data,
            ],
        ]);
    }

}
