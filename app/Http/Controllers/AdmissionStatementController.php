<?php

namespace App\Http\Controllers;

use App\Models\AdmissionStatement;
use App\Services\AdmissionStatementService;
use Illuminate\Http\Request;

class AdmissionStatementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private AdmissionStatementService $admissionStatementService;

    public function __construct(AdmissionStatementService $admissionStatementService)
    {
        $this->admissionStatementService = $admissionStatementService;
    }
    
    public function index($code)
    {
        return view('enrolment.form.student-approval',["code"=>$code]);
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
        $data = $request->all();
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
