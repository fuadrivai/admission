<?php

namespace App\Http\Controllers;

use App\Models\Admission;
use App\Models\AdmissionStatement;
use App\Models\StatementAgreement;
use App\Services\AdmissionStatementService;
use App\Services\FinancialDocumentService;
use App\Services\ParentStatementService;
use Illuminate\Http\Request;

class AdmissionStatementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private AdmissionStatementService $admissionStatementService;
    private ParentStatementService $parentStatementService;
    private FinancialDocumentService $financialDocumentService;

    public function __construct(AdmissionStatementService $admissionStatementService, ParentStatementService $parentStatementService, FinancialDocumentService $financialDocumentService)
    {
        $this->admissionStatementService = $admissionStatementService;
        $this->parentStatementService = $parentStatementService;
        $this->financialDocumentService = $financialDocumentService;
    }
    
    public function index($code)
    {
        $admission = Admission::where('code', $code)->first();
        $parentAgreementItemIds = [];

        if ($admission && $admission->statement) {
            $parentAgreementItemIds = StatementAgreement::where('admission_statement_id', $admission->statement->id)
                ->where('type', 'Parent')
                ->whereNotNull('statement_item_id')
                ->pluck('statement_item_id')
                ->map(fn ($value) => (int) $value)
                ->all();
        }

        return view('enrolment.form.student-approval', [
            'code' => $code,
            'parentStatementDocument' => $this->parentStatementService->getPublishedParentDocument(),
            'parentAgreementItemIds' => $parentAgreementItemIds,
            'financialDocument' => $this->financialDocumentService->getPublishedDocument(),
        ]);
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
        $data = $request->all();
        $statement = $this->admissionStatementService->postStatement($data);
        return response()->json($statement);
    }
    public function storeFinancial(Request $request)
    {
        $request->validate([
            'agree_financial_document' => 'accepted',
            'financial_document_id' => 'required|integer|exists:admission_financial_documents,id',
        ]);

        $data = $request->all();
        $data['ip_address'] = $request->ip();
        $data['user_agent'] = $request->userAgent();
        $statement = $this->admissionStatementService->postFinancial($data);
        return response()->json($statement);
    }
    public function getFinancial($id)
    {
        $financial = $this->admissionStatementService->getFinancial($id);
        return response()->json($financial);
    }

    public function postAgreement(Request $data)
    {
        $agreement = $this->admissionStatementService->postAgreement($data);
        return response()->json($agreement);
    }

    public function getAgreement($id,$role)
    {
        $agreement = $this->admissionStatementService->getAgreement($id,$role);
        return response()->json($agreement);
    }

    public function saveParentAgreement(Request $request)
    {
        $request->validate([
            'admission_statement_id' => 'required|exists:admission_statements,id',
            'statement_item_id' => 'nullable|array',
        ]);

        $items = $request->input('statement_item_id', []);
        $this->parentStatementService->saveParentAgreement($request->admission_statement_id, $items, $request);

        return response()->json([
            'success' => true,
            'count' => count($items),
        ]);
    }

    public function getParentAgreementIds($id)
    {
        $ids = StatementAgreement::where('admission_statement_id', $id)
            ->where('type', 'Parent')
            ->whereNotNull('statement_item_id')
            ->pluck('statement_item_id')
            ->map(fn ($value) => (int) $value)
            ->all();

        return response()->json($ids);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\AdmissionStatement  $admissionStatement
     * @return \Illuminate\Http\Response
     */
    public function show(AdmissionStatement $admissionStatement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\AdmissionStatement  $admissionStatement
     * @return \Illuminate\Http\Response
     */
    public function edit(AdmissionStatement $admissionStatement)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\AdmissionStatement  $admissionStatement
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, AdmissionStatement $admissionStatement)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\AdmissionStatement  $admissionStatement
     * @return \Illuminate\Http\Response
     */
    public function destroy(AdmissionStatement $admissionStatement)
    {
        //
    }
}
