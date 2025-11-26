<?php

namespace App\Http\Controllers;

use App\Models\Enrolment;
use App\Services\BranchService;
use App\Services\EnrolmentService;
use Illuminate\Http\Request;

class EnrolmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    private BranchService $branchService;
    private EnrolmentService $enrolmentService;
    public function __construct(BranchService $branchService, EnrolmentService $enrolmentService)
    {
        $this->branchService = $branchService;
        $this->enrolmentService = $enrolmentService;
    }
    public function index()
    {
        //
    }

    public function setting()
    {
        return view('enrolment.setting');
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
        //
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
        //
    }
    public function internal(Enrolment $enrolment)
    {
        return view('enrolment.form.internal', compact('enrolment'));
    }

    public function external()
    {
        $branches = $this->branchService->get();
        return view('enrolment.form.external', compact('branches'));
    }

    public function postExternal(Request $request)
    {
        $rules = [
            'alreadyVisit' => 'required',
            'code'         => 'nullable|string',
            'prospectsId'  => 'nullable|string',
            'branch'       => 'required',
            'level'        => 'required',
            'grade'        => 'required',
            'academicYear' => 'required|string',
            'parentName'   => 'required|string|max:255',
            'email'        => 'required',
            'phone'        => 'required|string|max:20',
            'relationship' => 'required|string',
            'zipCode'      => 'nullable|string|max:20',
            'address'      => 'required|string',
            'childName'    => 'required|string|max:255',
            'placeOfBirth' => 'required|string|max:120',
            'dateOfBirth'  => 'required',
            'currentSchool'=> 'nullable|string|max:255',
            'childSosmed'  => 'nullable|string|max:255',
            'opendayVisited'        => 'required',
            'knowledgeAboutProgram' => 'required|in:yes,no,maybe',
            'infoFrom'          => 'required|string',
            'infoFromMessage'   => 'nullable|string',
            'reasonForEnrolment'     => 'required|string',
            'prefferedProgram'       => 'required|string',
            'expectationMhisImpact'  => 'required|string',
            'recommenderName'      => 'nullable|string|max:255',
            'recommenderPhone'     => 'nullable|string|max:20',
            'recommenderChildName' => 'nullable|string|max:255',
            'recommenderChildClass'=> 'nullable|string|max:120',
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

    
}
