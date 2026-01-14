<?php

namespace App\Http\Controllers;

use App\Models\AdmissionStatement;
use App\Services\AdmissionService;
use Illuminate\Http\Request;

class AdmissionStatementController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private AdmissionService $admissionService;

    public function __construct(AdmissionService $admissionService)
    {
        $this->admissionService = $admissionService;
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
        //
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
